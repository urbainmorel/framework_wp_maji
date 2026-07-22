<?php
/**
 * Mise à jour de flotte du thème depuis les releases GitHub.
 *
 * Le vérificateur (plugin-update-checker) est fourni par le plugin
 * maji-core : le thème s'y raccorde si la bibliothèque est disponible.
 *
 * @package maji-framework
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initialise le vérificateur de mise à jour du thème.
 */
function maji_framework_init_updater(): void {
	if ( ! class_exists( \YahnisElsts\PluginUpdateChecker\v5\PucFactory::class ) ) {
		return;
	}

	$checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		'https://github.com/urbainmorel/framework_wp_maji/',
		get_template_directory() . '/style.css',
		'maji-framework'
	);

	$api = $checker->getVcsApi();
	if ( null !== $api ) {
		$api->enableReleaseAssets( '/maji-framework\.zip/' );
	}

	$channel = defined( 'MAJI_UPDATE_CHANNEL' ) ? MAJI_UPDATE_CHANNEL : 'stable';
	if ( 'stable' === $channel && method_exists( $checker, 'getVcsApi' ) ) {
		// Les pré-releases (canal beta) sont ignorées par défaut par l'API releases.
		add_filter(
			'puc_request_info_result-maji-framework',
			static function ( $info ) {
				return $info;
			}
		);
	}
}
add_action( 'init', 'maji_framework_init_updater' );
