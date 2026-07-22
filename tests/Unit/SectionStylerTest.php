<?php
/**
 * Tests des leviers de diversité par section (V2-B).
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Dna\SectionStyler;
use PHPUnit\Framework\TestCase;

/**
 * Rythme de fonds et variantes de style de section.
 */
final class SectionStylerTest extends TestCase {

	/**
	 * Fabrique une section neutre (fond `base`).
	 *
	 * @param string $slug Slug de la section.
	 */
	private function neutral( string $slug ): array {
		return [
			'slug'    => $slug,
			'content' => '<!-- wp:group {"align":"full","backgroundColor":"base","layout":{"type":"constrained"}} -->' . "\n"
				. '<div class="wp-block-group alignfull has-base-background-color has-background"><p>x</p></div>' . "\n"
				. '<!-- /wp:group -->',
		];
	}

	/**
	 * Fabrique une section à fond intentionnel (accent — CTA).
	 */
	private function accent( string $slug ): array {
		return [
			'slug'    => $slug,
			'content' => '<!-- wp:group {"align":"full","backgroundColor":"accent"} -->' . "\n"
				. '<div class="wp-block-group alignfull has-accent-background-color has-background"><p>x</p></div>' . "\n"
				. '<!-- /wp:group -->',
		];
	}

	/**
	 * Fabrique un hero (fond `base` mais slug « hero » ⇒ exclu).
	 */
	private function hero( string $slug ): array {
		return [
			'slug'    => $slug,
			'content' => '<!-- wp:group {"align":"full","backgroundColor":"base"} -->' . "\n"
				. '<div class="wp-block-group alignfull has-base-background-color has-background"><p>hero</p></div>' . "\n"
				. '<!-- /wp:group -->',
		];
	}

	/**
	 * `alterne` : le 1er neutre reste base, le 2e passe en surface.
	 */
	public function test_alterne_alternates_neutrals(): void {
		$sections = [ $this->neutral( 'maji/hotel-chambres-01' ), $this->neutral( 'maji/commun-avis-01' ) ];
		$out      = SectionStyler::apply( $sections, 'alterne', '' );

		self::assertStringContainsString( 'has-base-background-color', $out[0] );
		self::assertStringNotContainsString( 'surface', $out[0] );
		self::assertStringContainsString( 'has-surface-background-color', $out[1] );
		self::assertStringContainsString( '"backgroundColor":"surface"', $out[1] );
		self::assertStringNotContainsString( 'has-base-background-color', $out[1] );
	}

	/**
	 * `bandes` : alternance base / surface-alt.
	 */
	public function test_bandes_uses_surface_alt(): void {
		$sections = [ $this->neutral( 'a' ), $this->neutral( 'b' ) ];
		$out      = SectionStyler::apply( $sections, 'bandes', '' );
		self::assertStringContainsString( 'has-base-background-color', $out[0] );
		self::assertStringContainsString( 'has-surface-alt-background-color', $out[1] );
	}

	/**
	 * `contraste` : dès le 1er neutre on quitte `base` (surface / surface-alt).
	 */
	public function test_contraste_no_base(): void {
		$sections = [ $this->neutral( 'a' ), $this->neutral( 'b' ), $this->neutral( 'c' ) ];
		$out      = SectionStyler::apply( $sections, 'contraste', '' );
		self::assertStringContainsString( 'has-surface-background-color', $out[0] );
		self::assertStringContainsString( 'has-surface-alt-background-color', $out[1] );
		self::assertStringContainsString( 'has-surface-background-color', $out[2] );
	}

	/**
	 * `uni` (et valeur vide) : aucun changement de fond.
	 */
	public function test_uni_is_noop_for_background(): void {
		$sections = [ $this->neutral( 'a' ), $this->neutral( 'b' ) ];
		foreach ( [ 'uni', '' ] as $rhythm ) {
			$out = SectionStyler::apply( $sections, $rhythm, '' );
			self::assertStringContainsString( 'has-base-background-color', $out[0] );
			self::assertStringContainsString( 'has-base-background-color', $out[1] );
			self::assertStringNotContainsString( 'surface', $out[1] );
		}
	}

	/**
	 * Les sections à fond intentionnel (accent) ne sont jamais recolorées.
	 */
	public function test_accent_section_untouched(): void {
		$sections = [ $this->accent( 'maji/commun-cta-01' ), $this->neutral( 'b' ) ];
		$out      = SectionStyler::apply( $sections, 'alterne', '' );
		self::assertStringContainsString( 'has-accent-background-color', $out[0] );
		// Le 1er neutre reste l'index 0 (l'accent ne décale pas la parité) ⇒ base.
		self::assertStringContainsString( 'has-base-background-color', $out[1] );
	}

	/**
	 * Un hero n'est pas recoloré et ne décale pas la parité des neutres.
	 */
	public function test_hero_excluded_from_rhythm_and_parity(): void {
		$sections = [ $this->hero( 'maji/hotel-hero-03' ), $this->neutral( 'a' ), $this->neutral( 'b' ) ];
		$out      = SectionStyler::apply( $sections, 'alterne', '' );
		self::assertStringContainsString( 'has-base-background-color', $out[0] ); // hero intact
		self::assertStringContainsString( 'has-base-background-color', $out[1] ); // 1er neutre = base
		self::assertStringContainsString( 'has-surface-background-color', $out[2] ); // 2e neutre = surface
	}

	/**
	 * `section_style` ajoute la classe de block style (comment + div), hors heros.
	 */
	public function test_style_adds_block_style_class(): void {
		$sections = [ $this->hero( 'maji/resto-hero-01' ), $this->neutral( 'maji/resto-menu-01' ) ];
		$out      = SectionStyler::apply( $sections, 'uni', 'ombre' );

		self::assertStringNotContainsString( 'is-style-maji-ombre', $out[0] ); // hero exclu
		self::assertStringContainsString( '"className":"is-style-maji-ombre"', $out[1] );
		self::assertStringContainsString( 'wp-block-group is-style-maji-ombre ', $out[1] );
	}

	/**
	 * `section_style` = auto (ou vide) : aucune classe ajoutée.
	 */
	public function test_style_auto_is_noop(): void {
		$sections = [ $this->neutral( 'a' ) ];
		foreach ( [ 'auto', '' ] as $style ) {
			$out = SectionStyler::apply( $sections, 'uni', $style );
			self::assertStringNotContainsString( 'is-style-maji', $out[0] );
		}
	}

	/**
	 * Les deux leviers se combinent sur une même section neutre.
	 */
	public function test_combined_rhythm_and_style(): void {
		$sections = [ $this->neutral( 'a' ), $this->neutral( 'b' ) ];
		$out      = SectionStyler::apply( $sections, 'alterne', 'net' );

		self::assertStringContainsString( 'is-style-maji-net', $out[0] );
		self::assertStringContainsString( 'has-base-background-color', $out[0] );
		self::assertStringContainsString( 'is-style-maji-net', $out[1] );
		self::assertStringContainsString( 'has-surface-background-color', $out[1] );
	}
}
