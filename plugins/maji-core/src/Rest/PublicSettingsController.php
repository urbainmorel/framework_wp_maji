<?php
/**
 * Endpoint public `GET maji/v1/public-settings`.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Rest;

use MAJI\Core\Settings\Settings;

/**
 * Sous-ensemble non sensible des réglages pour les blocs front.
 */
final class PublicSettingsController {

	/**
	 * Constructeur.
	 *
	 * @param Settings $settings Réglages.
	 */
	public function __construct( private readonly Settings $settings ) {}

	/**
	 * Branche les routes REST.
	 */
	public function register(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Déclare la route.
	 */
	public function register_routes(): void {
		register_rest_route(
			'maji/v1',
			'/public-settings',
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_settings' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Retourne les réglages publics (jamais `integrations`).
	 */
	public function get_settings(): \WP_REST_Response {
		return new \WP_REST_Response( $this->settings->public_subset(), 200 );
	}
}
