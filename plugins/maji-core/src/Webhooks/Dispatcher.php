<?php
/**
 * Envoi des webhooks sortants signés, avec relances.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Webhooks;

use MAJI\Core\Hotel\Reservations;
use MAJI\Core\Settings\Settings;

/**
 * Écoute les événements métier, construit les payloads (Annexe A),
 * envoie vers n8n et planifie les relances (+1 min, +10 min, +60 min).
 */
final class Dispatcher {

	private const RETRY_DELAYS = [ MINUTE_IN_SECONDS, 10 * MINUTE_IN_SECONDS, HOUR_IN_SECONDS ];

	private const RETRY_HOOK = 'maji_webhook_retry';

	private const TIMEOUT = 5;

	/**
	 * Constructeur.
	 *
	 * @param Settings $settings Réglages.
	 */
	public function __construct( private readonly Settings $settings ) {}

	/**
	 * Branche les hooks métier.
	 */
	public function register(): void {
		add_action( 'maji_reservation_created', [ $this, 'on_reservation_created' ] );
		add_action( 'maji_reservation_status_changed', [ $this, 'on_reservation_status_changed' ], 10, 4 );
		add_action( 'woocommerce_checkout_order_processed', [ $this, 'on_order_created' ] );
		add_action( 'woocommerce_order_status_changed', [ $this, 'on_order_status_changed' ], 10, 3 );
		add_action( self::RETRY_HOOK, [ $this, 'retry' ], 10, 2 );
	}

	/**
	 * Nouvelle demande de réservation.
	 *
	 * @param int $reservation_id ID de la réservation.
	 */
	public function on_reservation_created( int $reservation_id ): void {
		$url = (string) $this->settings->get( 'integrations.n8n_reservation_url', '' );
		if ( '' === $url ) {
			return;
		}
		$payload = Payloads::reservation_created(
			$this->site_slug(),
			$this->now(),
			$this->collect_reservation( $reservation_id )
		);
		$this->deliver( 'reservation.created', $url, $payload, $reservation_id );
	}

	/**
	 * Changement de statut d'une réservation.
	 *
	 * @param int    $reservation_id ID.
	 * @param string $previous       Ancien statut.
	 * @param string $new_status     Nouveau statut.
	 * @param string $changed_by     Auteur.
	 */
	public function on_reservation_status_changed( int $reservation_id, string $previous, string $new_status, string $changed_by ): void {
		$url = (string) $this->settings->get( 'integrations.n8n_reservation_url', '' );
		if ( '' === $url ) {
			return;
		}
		$original = Payloads::reservation_created(
			$this->site_slug(),
			$this->now(),
			$this->collect_reservation( $reservation_id )
		);
		$payload  = Payloads::status_changed( $original, $previous, $new_status, $changed_by, $this->now() );
		$this->deliver( 'reservation.status_changed', $url, $payload, $reservation_id );
	}

	/**
	 * Nouvelle commande WooCommerce.
	 *
	 * @param int $order_id ID de la commande.
	 */
	public function on_order_created( int $order_id ): void {
		$url = (string) $this->settings->get( 'integrations.n8n_order_url', '' );
		if ( '' === $url ) {
			return;
		}
		$order_data = $this->collect_order( $order_id );
		if ( [] === $order_data ) {
			return;
		}
		$payload = Payloads::order_created( $this->site_slug(), $this->now(), $order_data );
		$this->deliver( 'order.created', $url, $payload, $order_id );
	}

	/**
	 * Changement de statut d'une commande WooCommerce.
	 *
	 * @param int    $order_id ID.
	 * @param string $previous Ancien statut.
	 * @param string $new_status Nouveau statut.
	 */
	public function on_order_status_changed( int $order_id, string $previous, string $new_status ): void {
		$url = (string) $this->settings->get( 'integrations.n8n_order_url', '' );
		if ( '' === $url ) {
			return;
		}
		$order_data = $this->collect_order( $order_id );
		if ( [] === $order_data ) {
			return;
		}
		$original = Payloads::order_created( $this->site_slug(), $this->now(), $order_data );
		$payload  = Payloads::status_changed( $original, $previous, $new_status, 'admin', $this->now() );
		$this->deliver( 'order.status_changed', $url, $payload, $order_id );
	}

	/**
	 * Envoie un webhook et journalise ; planifie une relance en échec.
	 *
	 * @param string               $event     Événement.
	 * @param string               $url       URL n8n.
	 * @param array<string, mixed> $payload   Payload complet.
	 * @param int                  $entity_id ID de l'entité source.
	 */
	public function deliver( string $event, string $url, array $payload, int $entity_id = 0 ): void {
		$body        = (string) wp_json_encode( $payload );
		$delivery_id = wp_generate_uuid4();
		$log_id      = Log::create( $event, $entity_id, $url, $delivery_id, $body );
		$this->send( $log_id, $event, $url, $body, $delivery_id, 0 );
	}

	/**
	 * Tentative d'envoi HTTP (initiale ou relance).
	 *
	 * @param int    $log_id      ID du journal.
	 * @param string $event       Événement.
	 * @param string $url         URL.
	 * @param string $body        Corps JSON.
	 * @param string $delivery_id UUID.
	 * @param int    $attempt     Numéro de tentative (0 = initiale).
	 */
	private function send( int $log_id, string $event, string $url, string $body, string $delivery_id, int $attempt ): void {
		$secret   = $this->settings->webhook_secret();
		$response = wp_remote_post(
			$url,
			[
				'timeout' => self::TIMEOUT,
				'headers' => [
					'Content-Type'     => 'application/json; charset=utf-8',
					'X-MAJI-Event'     => $event,
					'X-MAJI-Site'      => $this->site_slug(),
					'X-MAJI-Delivery'  => $delivery_id,
					'X-MAJI-Signature' => Payloads::sign( $body, $secret ),
				],
				'body'    => $body,
			]
		);

		if ( is_wp_error( $response ) ) {
			Log::record_attempt( $log_id, 0, $response->get_error_message() );
			$this->schedule_retry( $log_id, $event, $url, $body, $delivery_id, $attempt );
			return;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code >= 400 ) {
			Log::record_attempt( $log_id, $code, wp_remote_retrieve_response_message( $response ) );
			$this->schedule_retry( $log_id, $event, $url, $body, $delivery_id, $attempt );
			return;
		}

		Log::record_attempt( $log_id, $code );
	}

	/**
	 * Planifie la prochaine relance (Action Scheduler si présent, sinon WP-Cron).
	 *
	 * @param int    $log_id      ID du journal.
	 * @param string $event       Événement.
	 * @param string $url         URL.
	 * @param string $body        Corps JSON.
	 * @param string $delivery_id UUID.
	 * @param int    $attempt     Tentative venant d'échouer (0 = initiale).
	 */
	private function schedule_retry( int $log_id, string $event, string $url, string $body, string $delivery_id, int $attempt ): void {
		if ( $attempt >= count( self::RETRY_DELAYS ) ) {
			return;
		}
		$delay = self::RETRY_DELAYS[ $attempt ];
		$args  = [
			[
				'log_id'      => $log_id,
				'event'       => $event,
				'url'         => $url,
				'body'        => $body,
				'delivery_id' => $delivery_id,
			],
			$attempt + 1,
		];
		if ( function_exists( 'as_schedule_single_action' ) ) {
			as_schedule_single_action( time() + $delay, self::RETRY_HOOK, $args, 'maji-webhooks' );
		} else {
			wp_schedule_single_event( time() + $delay, self::RETRY_HOOK, $args );
		}
	}

	/**
	 * Exécute une relance planifiée.
	 *
	 * @param array<string, mixed> $job     Données de livraison.
	 * @param int                  $attempt Numéro de tentative.
	 */
	public function retry( array $job, int $attempt ): void {
		$this->send(
			(int) ( $job['log_id'] ?? 0 ),
			(string) ( $job['event'] ?? '' ),
			(string) ( $job['url'] ?? '' ),
			(string) ( $job['body'] ?? '' ),
			(string) ( $job['delivery_id'] ?? '' ),
			$attempt
		);
	}

	/**
	 * Renvoie manuellement une entrée du journal (bouton admin).
	 *
	 * Le payload complet n'est jamais stocké : il est reconstruit depuis
	 * l'entité source (commande ou réservation) au moment du renvoi.
	 *
	 * @param int $log_id ID du journal.
	 */
	public function resend( int $log_id ): bool {
		$row = Log::find( $log_id );
		if ( null === $row ) {
			return false;
		}
		$entity_id = (int) $row->entity_id;
		$event     = (string) $row->event;
		$url       = (string) $row->url;
		if ( 0 === $entity_id || '' === $url ) {
			return false;
		}

		if ( str_starts_with( $event, 'order.' ) ) {
			$order_data = $this->collect_order( $entity_id );
			if ( [] === $order_data ) {
				return false;
			}
			$payload = Payloads::order_created( $this->site_slug(), $this->now(), $order_data );
		} else {
			$payload = Payloads::reservation_created( $this->site_slug(), $this->now(), $this->collect_reservation( $entity_id ) );
		}

		$body = (string) wp_json_encode( $payload );
		$this->send( (int) $row->id, $event, $url, $body, (string) $row->delivery_id, 0 );
		return true;
	}

	/**
	 * Normalise une réservation pour le payload (Annexe A.2).
	 *
	 * @param int $reservation_id ID.
	 * @return array<string, mixed> Données.
	 */
	private function collect_reservation( int $reservation_id ): array {
		$room_id = (int) get_post_meta( $reservation_id, 'room_id', true );
		$type    = (string) get_post_meta( $reservation_id, 'type', true );

		$reservation = [
			'id'       => $reservation_id,
			'type'     => '' !== $type ? $type : 'room',
			'room'     => $room_id > 0 ? [
				'id'   => $room_id,
				'name' => get_the_title( $room_id ),
			] : null,
			'checkin'  => (string) get_post_meta( $reservation_id, 'checkin', true ),
			'checkout' => (string) get_post_meta( $reservation_id, 'checkout', true ),
			'guests'   => (int) get_post_meta( $reservation_id, 'guests', true ),
			'customer' => [
				'name'             => (string) get_post_meta( $reservation_id, 'name', true ),
				'phone'            => (string) get_post_meta( $reservation_id, 'phone', true ),
				'email'            => (string) get_post_meta( $reservation_id, 'email', true ),
				'whatsapp_consent' => (bool) get_post_meta( $reservation_id, 'whatsapp_consent', true ),
			],
			'message'  => (string) get_post_meta( $reservation_id, 'message', true ),
			'status'   => (string) get_post_meta( $reservation_id, 'status', true ),
		];

		if ( 'table' === $reservation['type'] ) {
			$reservation['date_time'] = (string) get_post_meta( $reservation_id, 'date_time', true );
			unset( $reservation['checkin'], $reservation['checkout'] );
		}

		return $reservation;
	}

	/**
	 * Normalise une commande WooCommerce pour le payload (Annexe A.1).
	 *
	 * @param int $order_id ID.
	 * @return array<string, mixed> Données (vide si commande introuvable).
	 */
	private function collect_order( int $order_id ): array {
		if ( ! function_exists( 'wc_get_order' ) ) {
			return [];
		}
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof \WC_Order ) {
			return [];
		}

		$items = [];
		foreach ( $order->get_items() as $item ) {
			if ( ! $item instanceof \WC_Order_Item_Product ) {
				continue;
			}
			$qty            = (int) $item->get_quantity();
			$total          = (float) $item->get_total();
			$variation_text = '';
			if ( $item->get_variation_id() > 0 ) {
				$product = $item->get_product();
				if ( $product instanceof \WC_Product_Variation ) {
					$variation_text = wc_get_formatted_variation( $product, true, false, false );
				}
			}
			$items[] = [
				'product_id' => (int) $item->get_product_id(),
				'name'       => $item->get_name(),
				'variation'  => $variation_text,
				'qty'        => $qty,
				'unit_price' => $qty > 0 ? (int) round( $total / $qty ) : 0,
				'total'      => (int) round( $total ),
			];
		}

		$fulfillment_mode = (string) $order->get_meta( 'maji_fulfillment_mode' );
		$zone             = (string) $order->get_meta( 'maji_delivery_zone' );
		$delivery_fee     = (int) round( (float) $order->get_total_fees() );

		return [
			'id'          => $order->get_id(),
			'number'      => $order->get_order_number(),
			'status'      => $order->get_status(),
			'customer'    => [
				'name'             => trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
				'phone'            => (string) $order->get_billing_phone(),
				'whatsapp_consent' => '1' === (string) $order->get_meta( 'maji_whatsapp_consent' ),
			],
			'fulfillment' => [
				'mode'        => '' !== $fulfillment_mode ? $fulfillment_mode : 'pickup',
				'zone'        => '' !== $zone ? $zone : null,
				'fee'         => $delivery_fee,
				'address'     => trim( $order->get_billing_address_1() . ', ' . $order->get_billing_city(), ', ' ),
				'pickup_time' => null,
			],
			'items'       => $items,
			'totals'      => [
				'subtotal' => (int) round( (float) $order->get_subtotal() ),
				'delivery' => $delivery_fee,
				'total'    => (int) round( (float) $order->get_total() ),
				'currency' => $order->get_currency(),
			],
			'payment'     => [ 'method' => (string) $order->get_payment_method() ],
			'note'        => (string) $order->get_customer_note(),
		];
	}

	/**
	 * Slug du site (host sans www).
	 */
	private function site_slug(): string {
		$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
		return sanitize_title( str_replace( 'www.', '', $host ) );
	}

	/**
	 * Horodatage ISO 8601 avec fuseau du site.
	 */
	private function now(): string {
		return (string) wp_date( 'c' );
	}
}
