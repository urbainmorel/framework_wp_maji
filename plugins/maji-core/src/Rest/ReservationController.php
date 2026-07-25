<?php
/**
 * Endpoint public `POST maji/v1/reservations`.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Rest;

use MAJI\Core\Hotel\Reservations;
use MAJI\Core\Support\Validator;

/**
 * Création de demandes de réservation (chambre ou table) depuis le front.
 *
 * Protections : honeypot, limitation par IP (5/heure), validation stricte.
 */
final class ReservationController {

	private const RATE_LIMIT     = 5;
	private const RATE_WINDOW    = HOUR_IN_SECONDS;
	private const RATE_TRANSIENT = 'maji_resa_rate_';

	/**
	 * Branche les routes REST.
	 */
	public function register(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Déclare les routes.
	 */
	public function register_routes(): void {
		register_rest_route(
			'maji/v1',
			'/reservations',
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'type'             => [
						'type'    => 'string',
						'enum'    => [ 'room', 'table' ],
						'default' => 'room',
					],
					'name'             => [
						'type'     => 'string',
						'required' => true,
					],
					'phone'            => [
						'type'     => 'string',
						'required' => true,
					],
					'email'            => [ 'type' => 'string' ],
					'message'          => [ 'type' => 'string' ],
					'room_id'          => [ 'type' => 'integer' ],
					'checkin'          => [ 'type' => 'string' ],
					'checkout'         => [ 'type' => 'string' ],
					'date_time'        => [ 'type' => 'string' ],
					'guests'           => [
						'type'    => 'integer',
						'default' => 1,
					],
					'whatsapp_consent' => [
						'type'    => 'boolean',
						'default' => false,
					],
					'website'          => [ 'type' => 'string' ],
				],
			]
		);
	}

	/**
	 * Crée la demande après validation.
	 *
	 * @param \WP_REST_Request $request Requête REST.
	 * @return \WP_REST_Response|\WP_Error Réponse.
	 */
	public function create( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		// Honeypot : le champ « website » doit rester vide (rempli par les robots).
		$honeypot = (string) $request->get_param( 'website' );
		if ( '' !== $honeypot ) {
			// Réponse neutre : ne pas informer le robot.
			return new \WP_REST_Response( [ 'message' => __( 'Votre demande a bien été envoyée.', 'maji-core' ) ], 201 );
		}

		// Limitation d'abus : clé par APPAREIL (IP + empreinte d'agent) pour ne pas
		// pénaliser les clients légitimes partageant une même IP publique derrière
		// le NAT opérateur (CGNAT), fréquent sur mobile en Afrique de l'Ouest.
		/**
		 * Nombre maximum de demandes par appareil et par fenêtre.
		 *
		 * @param int $limit Seuil (défaut 5).
		 */
		$limit = (int) apply_filters( 'maji_reservation_rate_limit', self::RATE_LIMIT );
		$key   = self::RATE_TRANSIENT . md5( $this->rate_fingerprint() );
		$hit   = (int) get_transient( $key );
		if ( $hit >= $limit ) {
			return new \WP_Error(
				'maji_rate_limited',
				__( 'Trop de demandes. Merci de réessayer dans une heure ou de nous contacter sur WhatsApp.', 'maji-core' ),
				[ 'status' => 429 ]
			);
		}

		$type  = (string) $request->get_param( 'type' );
		$name  = sanitize_text_field( (string) $request->get_param( 'name' ) );
		$phone = preg_replace( '/[\s\-\.]/', '', (string) $request->get_param( 'phone' ) ) ?? '';
		$email = sanitize_email( (string) $request->get_param( 'email' ) );

		if ( strlen( $name ) < 2 ) {
			return new \WP_Error( 'maji_invalid_name', __( 'Merci d\'indiquer votre nom.', 'maji-core' ), [ 'status' => 400 ] );
		}
		if ( ! Validator::is_e164( $phone ) ) {
			return new \WP_Error( 'maji_invalid_phone', __( 'Le numéro de téléphone doit être au format international, ex. +22901020304.', 'maji-core' ), [ 'status' => 400 ] );
		}

		$data = [
			'type'             => $type,
			'name'             => $name,
			'phone'            => $phone,
			'email'            => $email,
			'message'          => sanitize_textarea_field( (string) $request->get_param( 'message' ) ),
			'guests'           => max( 1, (int) $request->get_param( 'guests' ) ),
			'whatsapp_consent' => (bool) $request->get_param( 'whatsapp_consent' ),
		];

		if ( 'room' === $type ) {
			$checkin  = (string) $request->get_param( 'checkin' );
			$checkout = (string) $request->get_param( 'checkout' );
			if ( ! Validator::is_date_range( $checkin, $checkout ) ) {
				return new \WP_Error( 'maji_invalid_dates', __( 'Les dates d\'arrivée et de départ sont invalides.', 'maji-core' ), [ 'status' => 400 ] );
			}
			$data['checkin']  = $checkin;
			$data['checkout'] = $checkout;
			$room_id          = (int) $request->get_param( 'room_id' );
			if ( $room_id > 0 && \MAJI\Core\Hotel\Rooms::POST_TYPE === get_post_type( $room_id ) ) {
				$data['room_id'] = $room_id;
			}
		} else {
			$date_time = (string) $request->get_param( 'date_time' );
			if ( 1 !== preg_match( '/^\d{4}-\d{2}-\d{2} ([01]\d|2[0-3]):[0-5]\d$/', $date_time ) ) {
				return new \WP_Error( 'maji_invalid_datetime', __( 'La date et l\'heure sont invalides.', 'maji-core' ), [ 'status' => 400 ] );
			}
			$data['date_time'] = $date_time;
		}

		$post_id = Reservations::create( $data );
		if ( 0 === $post_id ) {
			return new \WP_Error( 'maji_creation_failed', __( 'Impossible d\'enregistrer la demande. Merci de réessayer.', 'maji-core' ), [ 'status' => 500 ] );
		}

		// On ne consomme le quota que sur une demande VALIDE et créée : les erreurs
		// de saisie ne bloquent pas l'utilisateur.
		set_transient( $key, $hit + 1, self::RATE_WINDOW );

		return new \WP_REST_Response(
			[
				'id'      => $post_id,
				'message' => __( 'Votre demande a bien été envoyée. Nous vous confirmons très vite par WhatsApp ou téléphone.', 'maji-core' ),
			],
			201
		);
	}

	/**
	 * Empreinte d'appareil pour la limitation : IP + agent utilisateur.
	 *
	 * Combiner l'IP et le User-Agent distingue des appareils différents derrière
	 * une même IP publique (CGNAT mobile), tout en restant anonyme (jamais stocké,
	 * seule l'empreinte hachée sert de clé de transient).
	 */
	private function rate_fingerprint(): string {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		return $ip . '|' . $ua;
	}
}
