<?php
/**
 * Tests du générateur de global styles utilisateur (STI §5.2).
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Dna\GlobalStyles;
use PHPUnit\Framework\TestCase;

/**
 * Fragment theme.json partiel depuis le bloc design.
 */
final class GlobalStylesTest extends TestCase {

	/**
	 * Le fragment est marqué comme global styles utilisateur v3.
	 */
	public function test_fragment_envelope(): void {
		$fragment = GlobalStyles::build( [] );
		self::assertSame( 3, $fragment['version'] );
		self::assertTrue( $fragment['isGlobalStylesUserThemeJSON'] );
	}

	/**
	 * La palette de l'ADN surcharge celle de la DA.
	 */
	public function test_palette_merge(): void {
		$fragment = GlobalStyles::build(
			[
				'da'      => 'da-editorial-sombre',
				'palette' => [ 'primary' => '#123456' ],
			]
		);
		$palette  = $fragment['settings']['color']['palette'];
		$by_slug  = array_column( $palette, 'color', 'slug' );
		// Surcharge ADN.
		self::assertSame( '#123456', $by_slug['primary'] );
		// Complété par la DA.
		self::assertSame( '#C9A227', $by_slug['accent'] );
		self::assertCount( 10, $palette );
	}

	/**
	 * radius_scale et spacing_mood modulent les tokens.
	 */
	public function test_radius_and_spacing(): void {
		$fragment = GlobalStyles::build(
			[
				'radius_scale' => 'lg',
				'spacing_mood' => 'aere',
			]
		);
		self::assertSame( '20px', $fragment['settings']['custom']['maji']['radius']['md'] );
		self::assertSame( 2.0, $fragment['settings']['spacing']['spacingScale']['mediumStep'] );
	}

	/**
	 * La paire typographique définit corps et titres.
	 */
	public function test_font_pair_applied(): void {
		$pair     = [
			'id'      => 'fp-01',
			'heading' => [ 'slug' => 'fraunces' ],
			'body'    => [ 'slug' => 'inter' ],
		];
		$fragment = GlobalStyles::build( [], $pair );
		self::assertSame( 'var(--wp--preset--font-family--inter)', $fragment['styles']['typography']['fontFamily'] );
		self::assertSame(
			'var(--wp--preset--font-family--fraunces)',
			$fragment['styles']['elements']['heading']['typography']['fontFamily']
		);
	}

	/**
	 * Le duotone n'apparaît que si demandé par image_treatment.
	 */
	public function test_duotone_only_when_requested(): void {
		$without = GlobalStyles::build( [ 'da' => 'da-editorial-sombre' ] );
		self::assertArrayNotHasKey( 'duotone', $without['settings']['color'] ?? [] );

		$with = GlobalStyles::build(
			[
				'da'              => 'da-editorial-sombre',
				'image_treatment' => [ 'duotone' => true ],
			]
		);
		self::assertNotEmpty( $with['settings']['color']['duotone'] );
	}
}
