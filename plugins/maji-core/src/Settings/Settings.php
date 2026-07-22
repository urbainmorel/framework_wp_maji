<?php
/**
 * Registre des réglages MAJI (option `maji_settings`).
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Settings;

/**
 * Accès typé, avec valeurs par défaut, à l'option unique `maji_settings`.
 *
 * Le secret des webhooks vit dans une option séparée non-autoload
 * (`maji_webhook_secret`) et n'est jamais exposé par cette classe.
 */
final class Settings {

	public const OPTION = 'maji_settings';

	public const SECRET_OPTION = 'maji_webhook_secret';

	/**
	 * Valeurs par défaut du schéma de réglages (STI §4.2).
	 *
	 * @return array<string, mixed> Schéma par défaut.
	 */
	public static function defaults(): array {
		return [
			'establishment'  => [
				'name'     => '',
				'type'     => 'hotel',
				'phone'    => '',
				'whatsapp' => '',
				'email'    => '',
				'address'  => [
					'street'   => '',
					'district' => '',
					'city'     => '',
					'country'  => 'BJ',
				],
				'hours'    => [
					'mon' => [],
					'tue' => [],
					'wed' => [],
					'thu' => [],
					'fri' => [],
					'sat' => [],
					'sun' => [],
				],
				'currency' => 'XOF',
				'locale'   => 'fr_FR',
				'socials'  => [
					'facebook'  => '',
					'instagram' => '',
					'tiktok'    => '',
				],
			],
			'features'       => [
				'ordering'      => true,
				'table_booking' => false,
				'delivery'      => true,
				'pickup'        => true,
			],
			'delivery_zones' => [],
			'integrations'   => [
				'n8n_order_url'       => '',
				'n8n_reservation_url' => '',
			],
		];
	}

	/**
	 * Retourne l'ensemble des réglages fusionnés avec les défauts.
	 *
	 * @return array<string, mixed> Réglages.
	 */
	public function all(): array {
		$stored = get_option( self::OPTION, [] );
		if ( ! is_array( $stored ) ) {
			$stored = [];
		}
		return $this->merge_recursive( self::defaults(), $stored );
	}

	/**
	 * Lit une valeur par chemin pointé (ex. `establishment.phone`).
	 *
	 * @param string $path     Chemin pointé.
	 * @param mixed  $fallback Valeur si absente.
	 * @return mixed Valeur trouvée ou fallback.
	 */
	public function get( string $path, mixed $fallback = null ): mixed {
		$value = $this->all();
		foreach ( explode( '.', $path ) as $segment ) {
			if ( ! is_array( $value ) || ! array_key_exists( $segment, $value ) ) {
				return $fallback;
			}
			$value = $value[ $segment ];
		}
		return $value;
	}

	/**
	 * Écrit une valeur par chemin pointé et persiste l'option.
	 *
	 * @param string $path  Chemin pointé.
	 * @param mixed  $value Valeur.
	 */
	public function set( string $path, mixed $value ): void {
		$settings = $this->all();
		$ref      = &$settings;
		$segments = explode( '.', $path );
		$last     = array_pop( $segments );
		foreach ( $segments as $segment ) {
			if ( ! isset( $ref[ $segment ] ) || ! is_array( $ref[ $segment ] ) ) {
				$ref[ $segment ] = [];
			}
			$ref = &$ref[ $segment ];
		}
		$ref[ $last ] = $value;
		update_option( self::OPTION, $settings );
	}

	/**
	 * Remplace l'intégralité des réglages (utilisé par apply-dna).
	 *
	 * @param array<string, mixed> $settings Nouveaux réglages.
	 */
	public function replace( array $settings ): void {
		update_option( self::OPTION, $this->merge_recursive( self::defaults(), $settings ) );
	}

	/**
	 * Sous-ensemble non sensible exposé au front (REST public-settings).
	 *
	 * @return array<string, mixed> Réglages publics (jamais `integrations`).
	 */
	public function public_subset(): array {
		$all = $this->all();
		unset( $all['integrations'] );
		return $all;
	}

	/**
	 * Récupère (ou génère) le secret HMAC des webhooks.
	 */
	public function webhook_secret(): string {
		$secret = get_option( self::SECRET_OPTION, '' );
		if ( ! is_string( $secret ) || '' === $secret ) {
			$secret = wp_generate_password( 48, false, false );
			add_option( self::SECRET_OPTION, $secret, '', false );
		}
		return $secret;
	}

	/**
	 * Fusion récursive préservant les clés par défaut absentes du stockage.
	 *
	 * @param array<string, mixed> $defaults Défauts.
	 * @param array<string, mixed> $stored   Valeurs stockées.
	 * @return array<string, mixed> Fusion.
	 */
	private function merge_recursive( array $defaults, array $stored ): array {
		foreach ( $stored as $key => $value ) {
			if ( is_array( $value ) && isset( $defaults[ $key ] ) && is_array( $defaults[ $key ] ) && ! array_is_list( $value ) ) {
				$defaults[ $key ] = $this->merge_recursive( $defaults[ $key ], $value );
			} else {
				$defaults[ $key ] = $value;
			}
		}
		return $defaults;
	}
}
