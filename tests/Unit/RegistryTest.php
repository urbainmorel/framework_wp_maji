<?php
/**
 * Tests du registre anti-clones (empreinte enrichie V2 — docs/DIVERSITE.md §4).
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
	 * Empreinte de référence (11 axes).
	 *
	 * @return array<string, mixed> Empreinte.
	 */
	private function base_fingerprint(): array {
		return [
			'site_slug'              => 'site-a',
			'da'                     => 'da-editorial-sombre',
			'hero'                   => 'maji/hotel-hero-03',
			'font_pair'              => 'fp-01',
			'palette_hue_bucket'     => 7,
			'header'                 => 'header-01',
			'footer'                 => 'footer-01',
			'image_treatment_bucket' => 'dark:duo:16:9',
			'section_bg_rhythm'      => 'alterne',
			'spacing_mood'           => 'aere',
			'composition_hash'       => sha1( 'a|b|c#net' ),
			'radius_scale'           => 'md',
		];
	}

	/**
	 * La table des poids somme à 1.0 et la trilogie de tête vaut le seuil.
	 */
	public function test_weights_sum_and_trilogy(): void {
		self::assertEqualsWithDelta( 1.0, array_sum( Registry::WEIGHTS ), 1e-9 );
		$trilogy = Registry::WEIGHTS['da'] + Registry::WEIGHTS['hero'] + Registry::WEIGHTS['font_pair'];
		self::assertEqualsWithDelta( Registry::THRESHOLD, $trilogy, 1e-9 );
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
			'site_slug'              => 'site-b',
			'da'                     => 'da-solaire-minimal',
			'hero'                   => 'maji/resto-hero-01',
			'font_pair'              => 'fp-03',
			'palette_hue_bucket'     => 2,
			'header'                 => 'header-02',
			'footer'                 => 'footer-02',
			'image_treatment_bucket' => 'none:flat:4:3',
			'section_bg_rhythm'      => 'uni',
			'spacing_mood'           => 'compact',
			'composition_hash'       => sha1( 'x|y#minimal' ),
			'radius_scale'           => 'sm',
		];
		self::assertSame( 0.0, Registry::similarity( $a, $b ) );
	}

	/**
	 * Cas limite : la trilogie (da + hero + font_pair) vaut exactement 0.70 ⇒ bloquant.
	 */
	public function test_exact_threshold_blocks(): void {
		$a = $this->base_fingerprint();
		$b = array_merge(
			$a,
			[
				'site_slug'              => 'site-b',
				'palette_hue_bucket'     => 3,
				'header'                 => 'header-04',
				'footer'                 => 'footer-02',
				'image_treatment_bucket' => 'none:flat:4:3',
				'section_bg_rhythm'      => 'uni',
				'spacing_mood'           => 'compact',
				'composition_hash'       => sha1( 'autre#minimal' ),
				'radius_scale'           => 'sm',
			]
		);
		// da (0.30) + hero (0.22) + font_pair (0.18) = 0.70.
		$score = Registry::similarity( $a, $b );
		self::assertSame( 0.7, $score );
		self::assertGreaterThanOrEqual( Registry::THRESHOLD, $score );
	}

	/**
	 * Sous le seuil : partager la DA, la paire typo et des axes secondaires
	 * sans le hero reste sous 0.70.
	 */
	public function test_below_threshold_passes(): void {
		$a = $this->base_fingerprint();
		$b = array_merge(
			$a,
			[
				'site_slug'              => 'site-b',
				'hero'                   => 'maji/hotel-hero-01',
				'header'                 => 'header-04',
				'footer'                 => 'footer-03',
				'image_treatment_bucket' => 'none:flat:4:3',
				'section_bg_rhythm'      => 'uni',
				'composition_hash'       => sha1( 'x#minimal' ),
			]
		);
		// da (0.30) + font_pair (0.18) + palette_hue_bucket (0.06)
		// + spacing_mood (0.03) + radius_scale (0.02) = 0.59.
		$score = Registry::similarity( $a, $b );
		self::assertSame( 0.59, $score );
		self::assertLessThan( Registry::THRESHOLD, $score );
	}

	/**
	 * Un axe vide ne compte jamais dans le score (rétrocompatibilité V1).
	 */
	public function test_empty_axes_do_not_count(): void {
		$a = array_merge(
			$this->base_fingerprint(),
			[
				'footer'                 => '',
				'image_treatment_bucket' => '',
				'section_bg_rhythm'      => '',
				'spacing_mood'           => '',
				'radius_scale'           => '',
			]
		);
		$b = array_merge( $a, [ 'site_slug' => 'site-b' ] );
		// Axes partagés non vides : da + hero + font_pair + palette_hue_bucket
		// + header + composition_hash = 0.30+0.22+0.18+0.06+0.05+0.03 = 0.84.
		self::assertSame( 0.84, Registry::similarity( $a, $b ) );
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
				'composition_hash'   => sha1( 'z#net' ),
			]
		);
		$far   = [
			'site_slug'              => 'site-loin',
			'da'                     => 'da-artisanal-texture',
			'hero'                   => 'maji/resto-hero-02',
			'font_pair'              => 'fp-06',
			'palette_hue_bucket'     => 0,
			'header'                 => 'header-03',
			'footer'                 => 'footer-03',
			'image_treatment_bucket' => 'primary:flat:3:2',
			'section_bg_rhythm'      => 'bandes',
			'spacing_mood'           => 'compact',
			'composition_hash'       => sha1( 'q#minimal' ),
			'radius_scale'           => 'sm',
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
	 * Le bucket de traitement d'image combine overlay, duotone et ratio.
	 */
	public function test_image_treatment_bucket(): void {
		self::assertSame( '', Registry::image_treatment_bucket( [] ) );
		self::assertSame(
			'dark:duo:16:9',
			Registry::image_treatment_bucket(
				[
					'overlay'    => 'dark',
					'duotone'    => true,
					'hero_ratio' => '16:9',
				]
			)
		);
		self::assertSame(
			'none:flat:',
			Registry::image_treatment_bucket( [ 'overlay' => 'none' ] )
		);
	}

	/**
	 * Le hash de composition intègre la variante de style de section.
	 */
	public function test_composition_hash(): void {
		self::assertSame( '', Registry::composition_hash( [], '' ) );
		$sections = [ 'maji/hotel-hero-03', 'maji/commun-cta-01' ];
		self::assertNotSame(
			Registry::composition_hash( $sections, 'net' ),
			Registry::composition_hash( $sections, 'ombre' )
		);
		self::assertSame(
			sha1( 'maji/hotel-hero-03|maji/commun-cta-01#net' ),
			Registry::composition_hash( $sections, 'net' )
		);
	}

	/**
	 * L'empreinte enrichie est construite depuis l'ADN.
	 */
	public function test_fingerprint_from_dna(): void {
		$dna = [
			'meta'      => [ 'site_slug' => 'hotel-test' ],
			'design'    => [
				'da'                => 'da-editorial-sombre',
				'font_pair'         => 'fp-01',
				'palette'           => [ 'primary' => '#FF0000' ],
				'radius_scale'      => 'lg',
				'spacing_mood'      => 'aere',
				'section_bg_rhythm' => 'alterne',
				'section_style'     => 'net',
				'image_treatment'   => [
					'overlay'    => 'dark',
					'duotone'    => true,
					'hero_ratio' => '16:9',
				],
			],
			'structure' => [
				'header' => 'header-01',
				'footer' => 'footer-02',
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
		self::assertSame( 'fp-01', $fingerprint['font_pair'] );
		self::assertSame( 0, $fingerprint['palette_hue_bucket'] );
		self::assertSame( 'header-01', $fingerprint['header'] );
		self::assertSame( 'footer-02', $fingerprint['footer'] );
		self::assertSame( 'dark:duo:16:9', $fingerprint['image_treatment_bucket'] );
		self::assertSame( 'alterne', $fingerprint['section_bg_rhythm'] );
		self::assertSame( 'aere', $fingerprint['spacing_mood'] );
		self::assertSame( 'lg', $fingerprint['radius_scale'] );
		self::assertSame(
			sha1( 'maji/hotel-hero-03|maji/commun-cta-01#net' ),
			$fingerprint['composition_hash']
		);
	}

	/**
	 * Un ADN V1 sans les nouveaux champs se fingerprint sans erreur : les
	 * axes optionnels sont vides et n'ajoutent pas de faux positif.
	 */
	public function test_fingerprint_backward_compatible(): void {
		$dna = [
			'meta'      => [ 'site_slug' => 'legacy-site' ],
			'design'    => [
				'da'        => 'da-solaire-minimal',
				'font_pair' => 'fp-03',
			],
			'structure' => [
				'header' => 'header-02',
				'footer' => 'footer-01',
				'pages'  => [
					[
						'slug'     => 'accueil',
						'sections' => [ 'maji/resto-hero-01' ],
					],
				],
			],
		];

		$fingerprint = Registry::fingerprint( $dna );
		self::assertSame( 'maji/resto-hero-01', $fingerprint['hero'] );
		self::assertSame( '', $fingerprint['image_treatment_bucket'] );
		self::assertSame( '', $fingerprint['section_bg_rhythm'] );
		self::assertSame( '', $fingerprint['spacing_mood'] );
		self::assertSame( '', $fingerprint['radius_scale'] );
		// composition_hash reste calculé (sections présentes, style vide).
		self::assertSame( sha1( 'maji/resto-hero-01#' ), $fingerprint['composition_hash'] );
	}
}
