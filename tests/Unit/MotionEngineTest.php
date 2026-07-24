<?php
/**
 * Garde-fou du moteur de motion piloté par l'ADN (V2-G2).
 *
 * Couvre le champ `design.motion` (validateur), les invariants du module GSAP
 * (accessibilité + compositor-only) et la présence des fichiers auto-hébergés.
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Dna\DnaValidator;
use PHPUnit\Framework\TestCase;

/**
 * Champ motion + module GSAP auto-hébergé.
 */
final class MotionEngineTest extends TestCase {

	private const THEME = __DIR__ . '/../../themes/maji-framework';

	/**
	 * ADN minimal valide, avec un `design.motion` donné.
	 *
	 * @param string $motion Valeur de motion.
	 * @return array<string, mixed> ADN.
	 */
	private function dna_with_motion( string $motion ): array {
		return [
			'schema'    => 'maji-dna/1',
			'meta'      => [ 'site_slug' => 'x', 'client' => 'X', 'created_at' => '2026-01-01' ],
			'identity'  => [ 'name' => 'X', 'type' => 'hotel', 'phone' => '+22990000000', 'whatsapp' => '+22990000000' ],
			'design'    => [ 'da' => 'da-editorial-sombre', 'font_pair' => 'fp-01', 'motion' => $motion ],
			'structure' => [
				'header'         => 'header-02',
				'footer'         => 'footer-01',
				'nav_vocabulary' => 'hotel-classique',
				'pages'          => [ [ 'slug' => 'accueil', 'title' => 'Accueil', 'sections' => [ 'maji/hotel-hero-01' ] ] ],
			],
			'content'   => [],
		];
	}

	/**
	 * Les quatre niveaux de motion sont acceptés.
	 */
	public function test_valid_motion_levels_accepted(): void {
		$validator = new DnaValidator();
		foreach ( [ 'none', 'subtle', 'standard', 'expressive' ] as $level ) {
			$errors = $validator->validate( $this->dna_with_motion( $level ) );
			foreach ( $errors as $error ) {
				self::assertStringNotContainsString( 'design.motion', $error, "$level inattendu : $error" );
			}
		}
	}

	/**
	 * Une valeur inconnue est rejetée avec un message actionnable.
	 */
	public function test_invalid_motion_rejected(): void {
		$errors = ( new DnaValidator() )->validate( $this->dna_with_motion( 'turbo' ) );
		$joined = implode( ' | ', $errors );
		self::assertStringContainsString( 'design.motion', $joined );
	}

	/**
	 * GSAP + ScrollTrigger sont auto-hébergés (jamais servis via CDN).
	 */
	public function test_gsap_is_vendored(): void {
		self::assertFileExists( self::THEME . '/assets/js/vendor/gsap/gsap.min.js' );
		self::assertFileExists( self::THEME . '/assets/js/vendor/gsap/ScrollTrigger.min.js' );
		self::assertFileExists( self::THEME . '/assets/js/vendor/gsap/NOTICE.md' );
	}

	/**
	 * L'enqueue ne référence aucun CDN tiers pour le motion.
	 */
	public function test_no_cdn_in_enqueue(): void {
		$php = (string) file_get_contents( self::THEME . '/inc/motion.php' );
		// Aucune URL absolue (donc aucun CDN) ni hôte CDN connu dans l'enqueue.
		self::assertDoesNotMatchRegularExpression( '#[a-z]+://#', $php );
		self::assertStringNotContainsStringIgnoringCase( 'jsdelivr', $php );
		self::assertStringNotContainsStringIgnoringCase( 'unpkg', $php );
		self::assertStringNotContainsStringIgnoringCase( 'cdnjs', $php );
		// Chargement différé et non bloquant.
		self::assertStringContainsString( "'strategy'  => 'defer'", $php );
	}

	/**
	 * Le module GSAP respecte accessibilité et compositor-only.
	 */
	public function test_gsap_module_invariants(): void {
		$js = (string) file_get_contents( self::THEME . '/assets/js/motion-gsap.js' );

		// Auto-garde + accessibilité.
		self::assertStringContainsString( 'window.gsap', $js );
		self::assertStringContainsString( 'gsap.matchMedia()', $js );
		self::assertStringContainsString( '(prefers-reduced-motion: no-preference)', $js );

		// Compositor-only : aucune PROPRIÉTÉ de layout animée (forme `prop:`).
		// (les mots-clés de position ScrollTrigger comme « top 85% » sont exclus.)
		foreach ( [ 'width', 'height', 'top', 'left', 'margin', 'padding' ] as $prop ) {
			self::assertSame(
				0,
				preg_match( '/\b' . $prop . '\s*:/', $js ),
				"Propriété de layout animée dans motion-gsap.js : $prop"
			);
		}
	}
}
