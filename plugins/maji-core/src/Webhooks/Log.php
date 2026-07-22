<?php
/**
 * Journal des webhooks : table `{prefix}maji_webhook_log`.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Webhooks;

/**
 * Création de table, insertion et lecture du journal.
 *
 * Jamais de secret ni de payload complet en clair : hash + extrait.
 */
final class Log {

	public const TABLE = 'maji_webhook_log';

	/**
	 * Crée (ou met à jour) la table du journal.
	 */
	public static function install(): void {
		global $wpdb;
		$table           = $wpdb->prefix . self::TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta(
			"CREATE TABLE {$table} (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				event VARCHAR(64) NOT NULL,
				entity_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
				url VARCHAR(500) NOT NULL,
				delivery_id VARCHAR(36) NOT NULL DEFAULT '',
				payload_hash VARCHAR(64) NOT NULL,
				payload_excerpt VARCHAR(300) NOT NULL DEFAULT '',
				status_code SMALLINT NOT NULL DEFAULT 0,
				attempts SMALLINT NOT NULL DEFAULT 0,
				last_error TEXT NULL,
				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,
				PRIMARY KEY  (id),
				KEY event (event),
				KEY created_at (created_at)
			) {$charset_collate};"
		);
	}

	/**
	 * Enregistre une nouvelle livraison.
	 *
	 * @param string $event       Événement.
	 * @param int    $entity_id   ID de l'entité source (commande / réservation).
	 * @param string $url         URL cible.
	 * @param string $delivery_id UUID de livraison.
	 * @param string $body        Corps JSON (hashé, jamais stocké entier).
	 * @return int ID de la ligne créée.
	 */
	public static function create( string $event, int $entity_id, string $url, string $delivery_id, string $body ): int {
		global $wpdb;
		$now = gmdate( 'Y-m-d H:i:s' );
		$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- table custom du plugin.
			$wpdb->prefix . self::TABLE,
			[
				'event'           => $event,
				'entity_id'       => $entity_id,
				'url'             => $url,
				'delivery_id'     => $delivery_id,
				'payload_hash'    => hash( 'sha256', $body ),
				'payload_excerpt' => mb_substr( $body, 0, 280 ),
				'status_code'     => 0,
				'attempts'        => 0,
				'created_at'      => $now,
				'updated_at'      => $now,
			],
			[ '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s' ]
		);
		return (int) $wpdb->insert_id;
	}

	/**
	 * Met à jour une livraison après tentative.
	 *
	 * @param int    $log_id      ID de la ligne.
	 * @param int    $status_code Code HTTP (0 si erreur réseau).
	 * @param string $error       Message d'erreur éventuel.
	 */
	public static function record_attempt( int $log_id, int $status_code, string $error = '' ): void {
		global $wpdb;
		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table custom du plugin.
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}maji_webhook_log
				SET attempts = attempts + 1, status_code = %d, last_error = %s, updated_at = %s
				WHERE id = %d",
				$status_code,
				mb_substr( $error, 0, 500 ),
				gmdate( 'Y-m-d H:i:s' ),
				$log_id
			)
		);
	}

	/**
	 * Dernières livraisons (200 max).
	 *
	 * @param int $limit Nombre de lignes.
	 * @return array<int, object> Lignes du journal.
	 */
	public static function recent( int $limit = 200 ): array {
		global $wpdb;
		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table custom du plugin, page admin.
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}maji_webhook_log ORDER BY id DESC LIMIT %d",
				max( 1, min( 200, $limit ) )
			)
		);
		return is_array( $rows ) ? $rows : [];
	}

	/**
	 * Récupère une ligne par ID.
	 *
	 * @param int $log_id ID.
	 */
	public static function find( int $log_id ): ?object {
		global $wpdb;
		$row = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table custom du plugin.
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}maji_webhook_log WHERE id = %d", $log_id )
		);
		return $row instanceof \stdClass ? $row : null;
	}
}
