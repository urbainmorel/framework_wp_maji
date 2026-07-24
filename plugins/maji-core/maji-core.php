<?php
/**
 * Plugin Name:       MAJI Core
 * Plugin URI:        https://github.com/urbainmorel/framework_wp_maji
 * Description:       Fonctionnalités métier des sites MAJI : modes hôtel/restaurant, réservations, commandes, WhatsApp, webhooks, SEO de base et outillage d'usine.
 * x-release-please-start-version
 * Version:           1.10.0
 * x-release-please-end-version
 * Requires at least: 6.6
 * Requires PHP:      8.1
 * Author:            MAJI
 * Author URI:        https://maji.digital
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       maji-core
 * Domain Path:       /languages
 *
 * @package maji-core
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAJI_CORE_VERSION', '1.10.0' ); // phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar -- release-please marker: x-release-please-version
define( 'MAJI_CORE_FILE', __FILE__ );
define( 'MAJI_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'MAJI_CORE_URL', plugin_dir_url( __FILE__ ) );

// Autoload Composer (PSR-4) si présent (build de release), sinon autoloader interne.
if ( file_exists( MAJI_CORE_DIR . 'vendor/autoload.php' ) ) {
	require_once MAJI_CORE_DIR . 'vendor/autoload.php';
} else {
	spl_autoload_register(
		static function ( string $class_name ): void {
			if ( ! str_starts_with( $class_name, 'MAJI\\Core\\' ) ) {
				return;
			}
			$relative = substr( $class_name, strlen( 'MAJI\\Core\\' ) );
			$path     = MAJI_CORE_DIR . 'src/' . str_replace( '\\', '/', $relative ) . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	);
}

register_activation_hook( __FILE__, [ \MAJI\Core\Plugin::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ \MAJI\Core\Plugin::class, 'deactivate' ] );

add_action(
	'plugins_loaded',
	static function (): void {
		\MAJI\Core\Plugin::instance()->init();
	}
);
