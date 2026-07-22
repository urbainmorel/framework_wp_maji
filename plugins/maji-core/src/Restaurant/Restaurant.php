<?php
/**
 * Module restaurant : plats Woo, zones de livraison, horaires, COD.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Restaurant;

use MAJI\Core\Settings\Settings;

/**
 * Intégration WooCommerce du mode restaurant (STI §4.2).
 */
final class Restaurant {

	/**
	 * Constructeur.
	 *
	 * @param Settings $settings Réglages.
	 */
	public function __construct( private readonly Settings $settings ) {}

	/**
	 * Branche les hooks (uniquement si WooCommerce est actif).
	 */
	public function register(): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Méta produit MAJI (disponibilité, badges).
		add_action( 'init', [ $this, 'register_product_meta' ] );
		add_action( 'woocommerce_product_options_general_product_data', [ $this, 'product_fields' ] );
		add_action( 'woocommerce_process_product_meta', [ $this, 'save_product_fields' ] );

		// Disponibilité : un plat indisponible n'est pas achetable.
		add_filter( 'woocommerce_is_purchasable', [ $this, 'filter_purchasable' ], 10, 2 );

		// Horaires : blocage panier/checkout hors plage (F-R4).
		add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'validate_open_hours' ] );
		add_action( 'woocommerce_check_cart_items', [ $this, 'check_cart_open_hours' ] );

		// Livraison / retrait + zone au checkout, frais par zone.
		add_action( 'woocommerce_before_order_notes', [ $this, 'checkout_fields' ] );
		add_action( 'woocommerce_checkout_process', [ $this, 'validate_checkout_fields' ] );
		add_action( 'woocommerce_cart_calculate_fees', [ $this, 'add_delivery_fee' ] );
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'save_order_meta' ] );
		add_action( 'woocommerce_checkout_update_order_review', [ $this, 'store_session_choice' ] );
	}

	/**
	 * Méta produit : disponibilité et badges.
	 */
	public function register_product_meta(): void {
		register_post_meta(
			'product',
			'maji_available',
			[
				'type'              => 'boolean',
				'single'            => true,
				'default'           => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => static fn(): bool => current_user_can( 'edit_products' ),
			]
		);
		register_post_meta(
			'product',
			'maji_badges',
			[
				'type'              => 'array',
				'single'            => true,
				'default'           => [],
				'show_in_rest'      => [
					'schema' => [
						'type'  => 'array',
						'items' => [
							'type' => 'string',
							'enum' => [ 'populaire', 'epice', 'nouveau' ],
						],
					],
				],
				'sanitize_callback' => static fn( $value ): array => array_values(
					array_intersect( is_array( $value ) ? $value : [], [ 'populaire', 'epice', 'nouveau' ] )
				),
				'auth_callback'     => static fn(): bool => current_user_can( 'edit_products' ),
			]
		);
	}

	/**
	 * Champs MAJI dans la fiche produit.
	 */
	public function product_fields(): void {
		global $post;
		$available = get_post_meta( $post->ID, 'maji_available', true );
		$badges    = get_post_meta( $post->ID, 'maji_badges', true );
		$badges    = is_array( $badges ) ? $badges : [];
		echo '<div class="options_group">';
		woocommerce_wp_checkbox(
			[
				'id'          => 'maji_available',
				'label'       => __( 'Disponible', 'maji-core' ),
				'description' => __( 'Décocher pour masquer le plat de la vente sans le dépublier.', 'maji-core' ),
				'value'       => ( '' === $available || '1' === $available || true === $available ) ? 'yes' : 'no',
			]
		);
		foreach ( \MAJI\Core\Restaurant\Menu::badge_labels() as $badge => $label ) {
			woocommerce_wp_checkbox(
				[
					'id'    => 'maji_badge_' . $badge,
					'label' => $label,
					'value' => in_array( $badge, $badges, true ) ? 'yes' : 'no',
				]
			);
		}
		echo '</div>';
	}

	/**
	 * Sauvegarde des champs produit.
	 *
	 * @param int $post_id ID du produit.
	 */
	public function save_product_fields( int $post_id ): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce vérifié par WooCommerce.
		update_post_meta( $post_id, 'maji_available', isset( $_POST['maji_available'] ) ? '1' : '0' );
		$badges = [];
		foreach ( array_keys( Menu::badge_labels() ) as $badge ) {
			if ( isset( $_POST[ 'maji_badge_' . $badge ] ) ) {
				$badges[] = $badge;
			}
		}
		update_post_meta( $post_id, 'maji_badges', $badges );
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Un plat indisponible n'est pas achetable.
	 *
	 * @param bool        $purchasable Achat possible.
	 * @param \WC_Product $product     Produit.
	 */
	public function filter_purchasable( bool $purchasable, \WC_Product $product ): bool {
		if ( '0' === (string) get_post_meta( $product->get_id(), 'maji_available', true ) ) {
			return false;
		}
		return $purchasable;
	}

	/**
	 * L'établissement est-il ouvert maintenant ?
	 */
	public function is_open_now(): bool {
		$hours = $this->settings->get( 'establishment.hours', [] );
		if ( ! is_array( $hours ) ) {
			return true;
		}
		$day_keys = [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ];
		$now_day  = $day_keys[ (int) wp_date( 'w' ) ];
		$now_time = (string) wp_date( 'H:i' );

		$ranges = isset( $hours[ $now_day ] ) && is_array( $hours[ $now_day ] ) ? $hours[ $now_day ] : [];
		if ( [] === $ranges ) {
			return false;
		}
		foreach ( $ranges as $range ) {
			if ( is_array( $range ) && count( $range ) >= 2 && $now_time >= $range[0] && $now_time <= $range[1] ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Blocage de l'ajout au panier hors horaires.
	 *
	 * @param bool $passed Validation en cours.
	 */
	public function validate_open_hours( bool $passed ): bool {
		if ( ! $this->is_open_now() ) {
			wc_add_notice(
				__( 'Nous sommes actuellement fermés : la carte reste consultable mais la commande est possible uniquement pendant nos horaires d\'ouverture.', 'maji-core' ),
				'error'
			);
			return false;
		}
		return $passed;
	}

	/**
	 * Blocage du checkout hors horaires.
	 */
	public function check_cart_open_hours(): void {
		if ( ! $this->is_open_now() ) {
			wc_add_notice(
				__( 'Nous sommes actuellement fermés : merci de repasser commande pendant nos horaires d\'ouverture.', 'maji-core' ),
				'error'
			);
		}
	}

	/**
	 * Champs livraison/retrait + zone + consentement WhatsApp au checkout.
	 */
	public function checkout_fields(): void {
		$features = $this->settings->get( 'features', [] );
		$zones    = $this->settings->get( 'delivery_zones', [] );
		if ( ! is_array( $features ) || ! is_array( $zones ) ) {
			return;
		}
		$delivery = ! empty( $features['delivery'] );
		$pickup   = ! empty( $features['pickup'] );

		echo '<div id="maji-fulfillment"><h3>' . esc_html__( 'Livraison ou retrait', 'maji-core' ) . '</h3>';

		$options = [];
		if ( $delivery ) {
			$options['delivery'] = __( 'Livraison', 'maji-core' );
		}
		if ( $pickup ) {
			$options['pickup'] = __( 'Retrait sur place', 'maji-core' );
		}
		woocommerce_form_field(
			'maji_fulfillment_mode',
			[
				'type'     => 'radio',
				'required' => true,
				'options'  => $options,
				'default'  => $delivery ? 'delivery' : 'pickup',
				'label'    => __( 'Comment souhaitez-vous récupérer votre commande ?', 'maji-core' ),
			]
		);

		if ( $delivery && [] !== $zones ) {
			$zone_options = [ '' => __( 'Choisissez votre quartier…', 'maji-core' ) ];
			foreach ( $zones as $zone ) {
				if ( is_array( $zone ) && isset( $zone['name'] ) ) {
					$zone_options[ (string) $zone['name'] ] = sprintf(
						'%s (+%s FCFA)',
						(string) $zone['name'],
						number_format_i18n( (int) ( $zone['fee'] ?? 0 ) )
					);
				}
			}
			woocommerce_form_field(
				'maji_delivery_zone',
				[
					'type'    => 'select',
					'options' => $zone_options,
					'label'   => __( 'Zone de livraison', 'maji-core' ),
				]
			);
		}

		woocommerce_form_field(
			'maji_whatsapp_consent',
			[
				'type'     => 'checkbox',
				'required' => true,
				'label'    => __( 'J\'accepte d\'être contacté·e sur WhatsApp pour cette commande.', 'maji-core' ),
			]
		);
		echo '</div>';
	}

	/**
	 * Validation des champs au checkout.
	 */
	public function validate_checkout_fields(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce vérifié par WooCommerce.
		$mode = isset( $_POST['maji_fulfillment_mode'] ) ? sanitize_key( wp_unslash( $_POST['maji_fulfillment_mode'] ) ) : '';
		if ( ! in_array( $mode, [ 'delivery', 'pickup' ], true ) ) {
			wc_add_notice( __( 'Merci de choisir entre livraison et retrait sur place.', 'maji-core' ), 'error' );
		}
		if ( 'delivery' === $mode ) {
			$zones = $this->settings->get( 'delivery_zones', [] );
			$zone  = isset( $_POST['maji_delivery_zone'] ) ? sanitize_text_field( wp_unslash( $_POST['maji_delivery_zone'] ) ) : '';
			if ( is_array( $zones ) && [] !== $zones && '' === $zone ) {
				wc_add_notice( __( 'Merci de choisir votre zone de livraison.', 'maji-core' ), 'error' );
			}
		}
		if ( empty( $_POST['maji_whatsapp_consent'] ) ) {
			wc_add_notice( __( 'Le consentement WhatsApp est nécessaire pour le suivi de votre commande.', 'maji-core' ), 'error' );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Mémorise le choix de zone en session (pour le recalcul des frais).
	 *
	 * @param string $posted_data Données sérialisées du checkout.
	 */
	public function store_session_choice( string $posted_data ): void {
		parse_str( $posted_data, $data );
		if ( null !== WC()->session ) {
			WC()->session->set( 'maji_fulfillment_mode', sanitize_key( (string) ( $data['maji_fulfillment_mode'] ?? '' ) ) );
			WC()->session->set( 'maji_delivery_zone', sanitize_text_field( (string) ( $data['maji_delivery_zone'] ?? '' ) ) );
		}
	}

	/**
	 * Ajoute les frais de livraison selon la zone choisie.
	 *
	 * @param \WC_Cart $cart Panier.
	 */
	public function add_delivery_fee( \WC_Cart $cart ): void {
		if ( null === WC()->session ) {
			return;
		}
		$mode = (string) WC()->session->get( 'maji_fulfillment_mode', '' );
		if ( 'delivery' !== $mode ) {
			return;
		}
		$zone_name = (string) WC()->session->get( 'maji_delivery_zone', '' );
		if ( '' === $zone_name ) {
			return;
		}
		$zones = $this->settings->get( 'delivery_zones', [] );
		if ( ! is_array( $zones ) ) {
			return;
		}
		foreach ( $zones as $zone ) {
			if ( is_array( $zone ) && ( $zone['name'] ?? '' ) === $zone_name ) {
				$fee = (int) ( $zone['fee'] ?? 0 );
				if ( $fee > 0 ) {
					/* translators: %s : nom de la zone de livraison. */
					$cart->add_fee( sprintf( __( 'Livraison — %s', 'maji-core' ), $zone_name ), $fee, false );
				}
				break;
			}
		}
	}

	/**
	 * Enregistre les méta MAJI sur la commande.
	 *
	 * @param int $order_id ID de la commande.
	 */
	public function save_order_meta( int $order_id ): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce vérifié par WooCommerce.
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof \WC_Order ) {
			return;
		}
		$mode = isset( $_POST['maji_fulfillment_mode'] ) ? sanitize_key( wp_unslash( $_POST['maji_fulfillment_mode'] ) ) : 'pickup';
		$zone = isset( $_POST['maji_delivery_zone'] ) ? sanitize_text_field( wp_unslash( $_POST['maji_delivery_zone'] ) ) : '';
		$order->update_meta_data( 'maji_fulfillment_mode', $mode );
		$order->update_meta_data( 'maji_delivery_zone', $zone );
		$order->update_meta_data( 'maji_whatsapp_consent', empty( $_POST['maji_whatsapp_consent'] ) ? '0' : '1' );
		$order->save();
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}
