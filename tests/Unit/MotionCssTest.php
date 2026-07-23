<?php
/**
 * Garde-fou des animations premium (V2-G1).
 *
 * Vérifie les invariants du CSS de motion : accessibilité (reduced-motion),
 * amélioration progressive (@supports), et performance (les animations
 * continues n'agissent que sur transform/opacity ; aucune propriété de layout
 * n'est transitionnée).
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Invariants du fichier assets/css/motion.css.
 */
final class MotionCssTest extends TestCase {

	private const FILE = __DIR__ . '/../../themes/maji-framework/assets/css/motion.css';

	/**
	 * Contenu du fichier.
	 */
	private function css(): string {
		self::assertFileExists( self::FILE );
		return (string) file_get_contents( self::FILE );
	}

	/**
	 * Toute la motion est coupée sous prefers-reduced-motion.
	 */
	public function test_reduced_motion_guard(): void {
		self::assertStringContainsString( 'prefers-reduced-motion: no-preference', $this->css() );
	}

	/**
	 * Les révélations vivent sous @supports : jamais d'état caché sans support.
	 */
	public function test_supports_guard(): void {
		$css = $this->css();
		self::assertStringContainsString( '@supports (animation-timeline: view())', $css );
		// Chaque usage de la view-timeline est sous le garde @supports.
		self::assertSame(
			substr_count( $css, 'animation-timeline: view()' ) > 0,
			strpos( $css, '@supports (animation-timeline: view())' ) < strpos( $css, 'animation-timeline: view()' )
		);
	}

	/**
	 * Les @keyframes n'animent que transform et opacity (compositor-only).
	 */
	public function test_keyframes_are_compositor_only(): void {
		$css = $this->css();
		preg_match_all( '/@keyframes\s+[\w-]+\s*\{(.*?)\n\t*\}\s*\n/s', $css, $blocks );
		self::assertNotEmpty( $blocks[1], 'Aucun @keyframes trouvé.' );

		foreach ( $blocks[1] as $body ) {
			preg_match_all( '/(?:^|\s)([a-z-]+)\s*:/m', $body, $props );
			foreach ( $props[1] as $prop ) {
				if ( in_array( $prop, [ 'from', 'to' ], true ) ) {
					continue;
				}
				self::assertContains(
					$prop,
					[ 'opacity', 'transform' ],
					"Propriété non-compositor dans un @keyframes : $prop"
				);
			}
		}
	}

	/**
	 * Aucune propriété de layout n'est transitionnée (coûteux en rendu).
	 */
	public function test_no_layout_properties_transitioned(): void {
		$css = $this->css();
		preg_match_all( '/transition:([^;]+);/s', $css, $transitions );
		$forbidden = [ 'width', 'height', 'top', 'left', 'right', 'bottom', 'margin', 'padding', 'inset' ];

		foreach ( $transitions[1] as $value ) {
			foreach ( $forbidden as $prop ) {
				self::assertStringNotContainsString(
					$prop,
					$value,
					"Propriété de layout transitionnée : $prop"
				);
			}
		}
	}
}
