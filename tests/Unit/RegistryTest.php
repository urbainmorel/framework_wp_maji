<?php
/**
 * Tests du registre anti-clones (STI §5.3).
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Dna\Registry;
use PHPUnit\Framework\TestCase;

/**
 * Empreintes, score de similarité et cas limites.
 */
final class RegistryTest extends TestCase {

	/**
	 * Empreinte de référence.
	 *
	 * @return array<string, mixed> Empreinte.
	 */
	private function base_fingerprint(): array {
		return [
			'site_slug'          => 'site-a',
			'da'                 => 'da-editorial-sombre',
			'font_pair'          => 'fp-01',
			'hero'               => 'maji/hotel-hero-03',
			'header'             => 'header-01',
			'palette_hue_bucket' => 7,
			'section_order_hash' => sha1( 'a|b|c' ),
		];
	}

	/**
	 * Deux sites identiques : score 1.0.
	 */
	public function test_identical_scores_one(): void {
		$a = $this->base_fingerprint();
		$b = array_merge( $a, [ 'site_slug' => 'site-b' ] );
		self::assertSame( 1.0, Registry::similarity( $a, $b ) );
	}

	/**
	 * Sites entièrement différents : score 0.
	 */
	public function test_disjoint_scores_zero(): void {
		$a = $this->base_fingerprint();
		$b = [
			'site_slug'          => 'site-b',
			'da'                 => 'da-solaire-minimal',
			'font_pair'          => 'fp-03',
			'hero'               => 'maji/resto-hero-01',
			'header'             => 'header-02',
			'palette_hue_bucket' => 2,
			'section_order_hash' => sha1( 'x|y' ),
		];
		self::assertSame( 0.0, Registry::similarity( $a, $b ) );
	}

	/**
	 * Cas limite : exactement 0.70 (da + font_pair + hero) ⇒ bloquant.
	 */
	public function test_exact_threshold_blocks(): void {
		$a = $this->base_fingerprint();
		$b = array_merge(
			$a,
			[
				'site_slug'          => 'site-b',
				'header'             => 'header-04',
				'palette_hue_bucket' => 3,
				'section_order_hash' => sha1( 'autre' ),
			]
		);
		// da (0.30) + font_pair (0.20) + hero (0.20) = 0.70.
		$score = Registry::similarity( $a, $b );
		self::assertSame( 0.7, $score );
		self::assertGreaterThanOrEqual( Registry::THRESHOLD, $score );
	}

	/**
	 * Sous le seuil : 0.50 ne bloque pas.
	 */
	public function test_below_threshold_passes(): void {
		$a = $this->base_fingerprint();
		$b = array_merge(
			$a,
			[
				'site_slug' => 'site-b',
				'font_pair' => 'fp-02',
				'hero'      => 'maji/hotel-hero-01',
				'header'    => 'header-04',
			]
		);
		// da (0.30) + palette_hue_bucket (0.15) + section_order_hash (0.05) = 0.50.
		$score = Registry::similarity( $a, $b );
		self::assertSame( 0.5, $score );
		self::assertLessThan( Registry::THRESHOLD, $score );
	}

	/**
	 * check() ignore l'entrée du même site (re-livraison).
	 */
	public function test_check_ignores_same_slug(): void {
		$a      = $this->base_fingerprint();
		$result = Registry::check( $a, [ $a ] );
		self::assertSame( 0.0, $result['max'] );
		self::assertNull( $result['closest'] );
	}

	/**
	 * check() identifie le site le plus proche et les axes en collision.
	 */
	public function test_check_finds_closest_and_axes(): void {
		$a     = $this->base_fingerprint();
		$close = array_merge(
			$a,
			[
				'site_slug'          => 'site-proche',
				'palette_hue_bucket' => 1,
				'section_order_hash' => sha1( 'z' ),
			]
		);
		$far   = [
			'site_slug'          => 'site-loin',
			'da'                 => 'da-artisanal-texture',
			'font_pair'          => 'fp-06',
			'hero'               => 'maji/resto-hero-02',
			'header'             => 'header-03',
			'palette_hue_bucket' => 0,
			'section_order_hash' => sha1( 'q' ),
		];

		$result = Registry::check( $a, [ $far, $close ] );
		self::assertSame( 'site-proche', $result['closest']['site_slug'] );
		self::assertContains( 'da', $result['axes'] );
		self::assertContains( 'font_pair', $result['axes'] );
		self::assertContains( 'hero', $result['axes'] );
		self::assertNotContains( 'palette_hue_bucket', $result['axes'] );
		self::assertNotEmpty( Registry::suggestions( $result['axes'] ) );
	}

	/**
	 * Quantification de teinte par tranches de 30°.
	 */
	public function test_hue_bucket(): void {
		self::assertSame( 0, Registry::hue_bucket( '#FF0000' ) );
		self::assertSame( 4, Registry::hue_bucket( '#00FF00' ) );
		self::assertSame( 8, Registry::hue_bucket( '#0000FF' ) );
	}

	/**
	 * L'empreinte est construite depuis l'ADN (hero = première section de l'accueil).
	 */
	public function test_fingerprint_from_dna(): void {
		$dna = [
			'meta'      => [ 'site_slug' => 'hotel-test' ],
			'design'    => [
				'da'        => 'da-editorial-sombre',
				'font_pair' => 'fp-01',
				'palette'   => [ 'primary' => '#FF0000' ],
			],
			'structure' => [
				'header' => 'header-01',
				'pages'  => [
					[
						'slug'     => 'accueil',
						'title'    => 'Accueil',
						'sections' => [ 'maji/hotel-hero-03', 'maji/commun-cta-01' ],
					],
				],
			],
		];

		$fingerprint = Registry::fingerprint( $dna );
		self::assertSame( 'hotel-test', $fingerprint['site_slug'] );
		self::assertSame( 'maji/hotel-hero-03', $fingerprint['hero'] );
		self::assertSame( 0, $fingerprint['palette_hue_bucket'] );
		self::assertSame( sha1( 'maji/hotel-hero-03|maji/commun-cta-01' ), $fingerprint['section_order_hash'] );
	}
}
