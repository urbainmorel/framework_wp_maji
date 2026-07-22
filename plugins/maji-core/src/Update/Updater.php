<?php
/**
 * Mise à jour de flotte du plugin depuis les releases GitHub (STI §7).
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Update;

/**
 * Branche plugin-update-checker v5 sur les releases du dépôt.
 */
final class Updater {

	private const REPO_URL = 'https://github.com/urbainmorel/framework_wp_maji/';

	/**
	 * Initialise le vérificateur de mise à jour.
	 */
	public function register(): void {
		if ( ! class_exists( \YahnisElsts\PluginUpdateChecker\v5\PucFactory::class ) ) {
			// Bibliothèque absente (installation de dev sans composer install) : pas de mise à jour auto.
			return;
		}

		$checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
			self::REPO_URL,
			MAJI_CORE_FILE,
			'maji-core'
		);

		$api = $checker->getVcsApi();
		if ( null !== $api ) {
			// L'asset de release `maji-core.zip` est construit par la CI au tag vX.Y.Z.
			$api->enableReleaseAssets( '/maji-core\.zip/' );
		}
	}

	/**
	 * Canal de mise à jour (stable|beta).
	 */
	public static function channel(): string {
		$channel = defined( 'MAJI_UPDATE_CHANNEL' ) ? (string) MAJI_UPDATE_CHANNEL : 'stable';
		return in_array( $channel, [ 'stable', 'beta' ], true ) ? $channel : 'stable';
	}
}
