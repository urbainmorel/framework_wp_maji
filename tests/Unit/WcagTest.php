<?php
/**
 * Tests du calcul de contraste WCAG.
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Support\Wcag;
use PHPUnit\Framework\TestCase;

/**
 * Ratio de contraste, niveau AA et teinte HSL.
 */
final class WcagTest extends TestCase {

	/**
	 * Noir sur blanc = ratio maximal 21:1.
	 */
	public function test_black_on_white_is_21(): void {
		self::assertEqualsWithDelta( 21.0, Wcag::contrast_ratio( '#000000', '#FFFFFF' ), 0.01 );
	}

	/**
	 * Une couleur sur elle-même = 1:1.
	 */
	public function test_same_color_is_1(): void {
		self::assertEqualsWithDelta( 1.0, Wcag::contrast_ratio( '#C9A227', '#C9A227' ), 0.001 );
	}

	/**
	 * Les palettes des 3 DA respectent AA sur les paires critiques.
	 */
	public function test_da_palettes_pass_aa(): void {
		// da-editorial-sombre : ink / surface, primary-contrast / primary, accent-contrast / accent.
		self::assertTrue( Wcag::passes_aa( '#E9E4D8', '#121820' ) );
		self::assertTrue( Wcag::passes_aa( '#FFFFFF', '#0D1B2A' ) );
		self::assertTrue( Wcag::passes_aa( '#141414', '#C9A227' ) );
		// da-solaire-minimal.
		self::assertTrue( Wcag::passes_aa( '#232A30', '#F5F6F4' ) );
		// da-artisanal-texture.
		self::assertTrue( Wcag::passes_aa( '#3A2E22', '#EFE5D5' ) );
		self::assertTrue( Wcag::passes_aa( '#FDF7EE', '#8A3B12' ) );
	}

	/**
	 * Une paire à faible contraste échoue.
	 */
	public function test_low_contrast_fails_aa(): void {
		self::assertFalse( Wcag::passes_aa( '#777777', '#888888' ) );
	}

	/**
	 * Teintes HSL de couleurs de référence.
	 */
	public function test_hue(): void {
		self::assertEqualsWithDelta( 0.0, Wcag::hue( '#FF0000' ), 0.5 );
		self::assertEqualsWithDelta( 120.0, Wcag::hue( '#00FF00' ), 0.5 );
		self::assertEqualsWithDelta( 240.0, Wcag::hue( '#0000FF' ), 0.5 );
		self::assertEqualsWithDelta( 0.0, Wcag::hue( '#FFFFFF' ), 0.5 );
	}
}
