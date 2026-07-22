<?php
/**
 * Gestion des modes hôtel / restaurant.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Modes;

use MAJI\Core\Settings\Settings;

/**
 * Détermine les modules actifs selon le type d'établissement.
 */
final class ModeManager {

	/**
	 * Constructeur.
	 *
	 * @param Settings $settings Réglages MAJI.
	 */
	public function __construct( private readonly Settings $settings ) {}

	/**
	 * Type d'établissement : hotel, restaurant ou mixte.
	 */
	public function type(): string {
		$type = $this->settings->get( 'establishment.type', 'hotel' );
		return in_array( $type, [ 'hotel', 'restaurant', 'mixte' ], true ) ? $type : 'hotel';
	}

	/**
	 * Le mode hôtel est-il actif ?
	 */
	public function is_hotel(): bool {
		return in_array( $this->type(), [ 'hotel', 'mixte' ], true );
	}

	/**
	 * Le mode restaurant est-il actif ?
	 */
	public function is_restaurant(): bool {
		return in_array( $this->type(), [ 'restaurant', 'mixte' ], true );
	}
}
