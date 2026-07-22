<?php
/**
 * Constructeurs de payloads de webhooks (contrat exact : STI Annexe A).
 *
 * Classe pure (sans dépendance WordPress) pour être testée unitairement
 * avec les fixtures de l'Annexe A.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Webhooks;

/**
 * Enveloppes `maji-webhook/1`.
 */
final class Payloads {

	public const SCHEMA = 'maji-webhook/1';

	/**
	 * Enveloppe commune.
	 *
	 * @param string               $event   Nom de l'événement.
	 * @param string               $site    Slug du site.
	 * @param string               $sent_at Date ISO 8601.
	 * @param array<string, mixed> $body    Corps spécifique (order / reservation).
	 * @return array<string, mixed> Payload complet.
	 */
	private static function envelope( string $event, string $site, string $sent_at, array $body ): array {
		return array_merge(
			[
				'schema'  => self::SCHEMA,
				'event'   => $event,
				'site'    => $site,
				'sent_at' => $sent_at,
			],
			$body
		);
	}

	/**
	 * Payload `order.created` (Annexe A.1).
	 *
	 * @param string               $site    Slug du site.
	 * @param string               $sent_at Date ISO 8601.
	 * @param array<string, mixed> $order   Données de commande normalisées.
	 * @return array<string, mixed> Payload.
	 */
	public static function order_created( string $site, string $sent_at, array $order ): array {
		return self::envelope( 'order.created', $site, $sent_at, [ 'order' => $order ] );
	}

	/**
	 * Payload `reservation.created` (Annexe A.2).
	 *
	 * @param string               $site        Slug du site.
	 * @param string               $sent_at     Date ISO 8601.
	 * @param array<string, mixed> $reservation Données de réservation normalisées.
	 * @return array<string, mixed> Payload.
	 */
	public static function reservation_created( string $site, string $sent_at, array $reservation ): array {
		return self::envelope( 'reservation.created', $site, $sent_at, [ 'reservation' => $reservation ] );
	}

	/**
	 * Payload `*.status_changed` (Annexe A.3) : enveloppe d'origine enrichie.
	 *
	 * @param array<string, mixed> $original_payload Payload d'origine (order.created / reservation.created).
	 * @param string               $previous         Ancien statut.
	 * @param string               $new_status       Nouveau statut.
	 * @param string               $changed_by       Auteur du changement.
	 * @param string               $sent_at          Nouvelle date d'envoi ISO 8601.
	 * @return array<string, mixed> Payload.
	 */
	public static function status_changed( array $original_payload, string $previous, string $new_status, string $changed_by, string $sent_at ): array {
		$event                               = str_replace( '.created', '.status_changed', (string) ( $original_payload['event'] ?? '' ) );
		$original_payload['event']           = $event;
		$original_payload['sent_at']         = $sent_at;
		$original_payload['previous_status'] = $previous;
		$original_payload['new_status']      = $new_status;
		$original_payload['changed_by']      = $changed_by;
		return $original_payload;
	}

	/**
	 * Signature HMAC-SHA256 du corps.
	 *
	 * @param string $body   Corps JSON.
	 * @param string $secret Secret partagé.
	 * @return string Valeur de l'en-tête `X-MAJI-Signature`.
	 */
	public static function sign( string $body, string $secret ): string {
		return 'sha256=' . hash_hmac( 'sha256', $body, $secret );
	}
}
