<?php
/**
 * Validations partagées (téléphone, dates, couleurs).
 *
 * Classe pure (sans dépendance WordPress) pour être testable unitairement
 * et réutilisée par le validateur d'ADN.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Support;

/**
 * Fonctions de validation réutilisables.
 */
final class Validator {

	/**
	 * Valide un numéro de téléphone au format E.164 (+22901234567).
	 *
	 * @param string $phone Numéro à valider.
	 */
	public static function is_e164( string $phone ): bool {
		return 1 === preg_match( '/^\+[1-9]\d{6,14}$/', $phone );
	}

	/**
	 * Valide une date au format Y-m-d.
	 *
	 * @param string $date Date à valider.
	 */
	public static function is_date( string $date ): bool {
		$dt = \DateTimeImmutable::createFromFormat( 'Y-m-d', $date );
		return false !== $dt && $dt->format( 'Y-m-d' ) === $date;
	}

	/**
	 * Valide une heure au format H:i.
	 *
	 * @param string $time Heure à valider.
	 */
	public static function is_time( string $time ): bool {
		return 1 === preg_match( '/^([01]\d|2[0-3]):[0-5]\d$/', $time );
	}

	/**
	 * Valide un code devise ISO 4217 (3 lettres majuscules).
	 *
	 * @param string $currency Code devise.
	 */
	public static function is_currency( string $currency ): bool {
		return 1 === preg_match( '/^[A-Z]{3}$/', $currency );
	}

	/**
	 * Valide une couleur hexadécimale (#RGB ou #RRGGBB).
	 *
	 * @param string $color Couleur.
	 */
	public static function is_hex_color( string $color ): bool {
		return 1 === preg_match( '/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color );
	}

	/**
	 * Vérifie que la date de départ est strictement après la date d'arrivée.
	 *
	 * @param string $checkin  Date d'arrivée (Y-m-d).
	 * @param string $checkout Date de départ (Y-m-d).
	 */
	public static function is_date_range( string $checkin, string $checkout ): bool {
		if ( ! self::is_date( $checkin ) || ! self::is_date( $checkout ) ) {
			return false;
		}
		return $checkout > $checkin;
	}
}
