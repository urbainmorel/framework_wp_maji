<?php
/**
 * Enregistrement des blocs dynamiques MAJI.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Blocks;

/**
 * Déclare la catégorie « MAJI » et enregistre tous les blocs du dossier blocks/.
 */
final class Blocks {

	/**
	 * Blocs disponibles (sous-dossiers de blocks/).
	 *
	 * @var string[]
	 */
	private const BLOCKS = [
		'reservation-form',
		'table-booking-form',
		'rooms-grid',
		'menu-grid',
		'menu-list',
		'menu-categories',
		'whatsapp-button',
		'opening-hours',
		'establishment-info',
	];

	/**
	 * Branche les hooks.
	 */
	public function register(): void {
		add_filter( 'block_categories_all', [ $this, 'register_category' ] );
		add_action( 'init', [ $this, 'register_blocks' ] );
	}

	/**
	 * Ajoute la catégorie MAJI en tête de liste.
	 *
	 * @param array<int, array<string, mixed>> $categories Catégories existantes.
	 * @return array<int, array<string, mixed>> Catégories.
	 */
	public function register_category( array $categories ): array {
		array_unshift(
			$categories,
			[
				'slug'  => 'maji',
				'title' => __( 'MAJI', 'maji-core' ),
				'icon'  => null,
			]
		);
		return $categories;
	}

	/**
	 * Enregistre chaque bloc depuis son block.json.
	 */
	public function register_blocks(): void {
		foreach ( self::BLOCKS as $block ) {
			$dir = MAJI_CORE_DIR . 'blocks/' . $block;
			if ( file_exists( $dir . '/block.json' ) ) {
				register_block_type( $dir );
			}
		}
	}
}
