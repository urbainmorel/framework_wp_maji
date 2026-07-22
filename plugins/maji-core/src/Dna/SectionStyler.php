<?php
/**
 * Application des leviers de diversité par section (V2-B).
 *
 * Classe pure : transforme le markup des sections d'une page AU MOMENT DE
 * L'IMPORT (contenu écrit en base, jamais dans les fichiers du thème — contrat
 * STI §5.2). Deux leviers, issus du bloc `design` de l'ADN :
 *
 * - `section_bg_rhythm` (uni|alterne|bandes|contraste) : alterne le fond des
 *   sections *neutres* (fond `base`) parmi les neutres déjà validés WCAG de la
 *   DA (`base`/`surface`/`surface-alt`). Les sections à fond intentionnel
 *   (CTA accent, heros en image) ne sont jamais touchées.
 * - `section_style` (auto|net|ombre|minimal) : applique une variante de block
 *   style (`is-style-maji-*`) au conteneur des sections (hors heros). Le CSS
 *   correspondant est fourni par les block styles du plugin (tokens only).
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Dna;

/**
 * Transforme les sections d'une page selon le rythme de fonds et le style.
 */
final class SectionStyler {

	/**
	 * Variantes de style de section reconnues (`auto` = aucune surcharge).
	 *
	 * @var string[]
	 */
	public const STYLES = [ 'net', 'ombre', 'minimal' ];

	/**
	 * Swatches de fond par rythme, pour les sections neutres dans l'ordre.
	 * `uni` (absent) ⇒ pas d'alternance. Toutes les valeurs sont des neutres
	 * clairs de la DA, contrôlés WCAG contre `ink` par le validateur.
	 *
	 * @var array<string, array{0: string, 1: string}>
	 */
	private const RHYTHM_SWATCHES = [
		'alterne'   => [ 'base', 'surface' ],
		'bandes'    => [ 'base', 'surface-alt' ],
		'contraste' => [ 'surface', 'surface-alt' ],
	];

	/**
	 * Applique les deux leviers à une liste ordonnée de sections.
	 *
	 * @param array<int, array{slug: string, content: string}> $sections Sections de la page.
	 * @param string                                           $rhythm   Valeur section_bg_rhythm.
	 * @param string                                           $style    Valeur section_style.
	 * @return string[] Contenus transformés, dans l'ordre.
	 */
	public static function apply( array $sections, string $rhythm, string $style ): array {
		$out           = [];
		$neutral_index = 0;

		foreach ( $sections as $section ) {
			$slug    = (string) ( $section['slug'] ?? '' );
			$content = (string) ( $section['content'] ?? '' );
			$is_hero = str_contains( $slug, 'hero' );

			if ( ! $is_hero && in_array( $style, self::STYLES, true ) ) {
				$content = self::add_block_style( $content, 'maji-' . $style );
			}

			if ( ! $is_hero && self::is_neutral( $content ) ) {
				if ( isset( self::RHYTHM_SWATCHES[ $rhythm ] ) ) {
					$swatch = self::RHYTHM_SWATCHES[ $rhythm ][ $neutral_index % 2 ];
					if ( 'base' !== $swatch ) {
						$content = self::set_first_background( $content, $swatch );
					}
				}
				++$neutral_index;
			}

			$out[] = $content;
		}

		return $out;
	}

	/**
	 * Une section est « neutre » si le fond de son premier groupe est `base`.
	 *
	 * @param string $content Markup de la section.
	 */
	private static function is_neutral( string $content ): bool {
		if ( 1 === preg_match( '/"backgroundColor":"([a-z-]+)"/', $content, $m ) ) {
			return 'base' === $m[1];
		}
		return false;
	}

	/**
	 * Remplace le premier fond `base` par le swatch demandé (attribut + classe).
	 *
	 * @param string $content Markup.
	 * @param string $swatch  Slug de couleur cible.
	 */
	private static function set_first_background( string $content, string $swatch ): string {
		$content = (string) preg_replace(
			'/"backgroundColor":"base"/',
			'"backgroundColor":"' . $swatch . '"',
			$content,
			1
		);
		return (string) preg_replace(
			'/has-base-background-color/',
			'has-' . $swatch . '-background-color',
			$content,
			1
		);
	}

	/**
	 * Ajoute la classe de block style au premier groupe de la section.
	 *
	 * @param string $content    Markup.
	 * @param string $style_name Nom de block style (ex. « maji-net »).
	 */
	private static function add_block_style( string $content, string $style_name ): string {
		$class = 'is-style-' . $style_name;

		$content = (string) preg_replace(
			'/<!-- wp:group \{/',
			'<!-- wp:group {"className":"' . $class . '",',
			$content,
			1
		);
		return (string) preg_replace(
			'/<div class="wp-block-group /',
			'<div class="wp-block-group ' . $class . ' ',
			$content,
			1
		);
	}
}
