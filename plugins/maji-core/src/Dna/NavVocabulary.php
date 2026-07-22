<?php
/**
 * Jeux de vocabulaire de navigation (F-U4) — ≥ 2 par secteur.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Dna;

/**
 * Libellés de navigation variables par site.
 */
final class NavVocabulary {

	/**
	 * Jeux disponibles : identifiant → libellés par page type.
	 *
	 * @return array<string, array<string, string>> Jeux.
	 */
	public static function sets(): array {
		return [
			'hotel-classique'    => [
				'accueil'  => 'Accueil',
				'chambres' => 'Chambres',
				'services' => 'Services',
				'contact'  => 'Contact',
			],
			'hotel-experientiel' => [
				'accueil'  => 'Bienvenue',
				'chambres' => 'Séjourner',
				'services' => 'Expériences',
				'contact'  => 'Nous trouver',
			],
			'resto-classique'    => [
				'accueil'   => 'Accueil',
				'menu'      => 'Menu',
				'commander' => 'Commander',
				'contact'   => 'Contact',
			],
			'resto-convivial'    => [
				'accueil'   => 'Bienvenue',
				'menu'      => 'La Carte',
				'commander' => 'Se régaler',
				'contact'   => 'Venir nous voir',
			],
		];
	}

	/**
	 * Un jeu existe-t-il ?
	 *
	 * @param string $set_id Identifiant du jeu.
	 */
	public static function exists( string $set_id ): bool {
		return array_key_exists( $set_id, self::sets() );
	}

	/**
	 * Libellé d'une page dans un jeu (repli : titre de la page).
	 *
	 * @param string $set_id    Jeu.
	 * @param string $page_slug Slug de page.
	 * @param string $fallback  Libellé de repli.
	 */
	public static function label( string $set_id, string $page_slug, string $fallback ): string {
		$sets = self::sets();
		return $sets[ $set_id ][ $page_slug ] ?? $fallback;
	}
}
