<?php
/**
 * Garde-fou du catalogue de paires typographiques (V2-E).
 *
 * Vérifie que les 12 paires sont complètes, uniques, référencent des familles
 * enregistrées dans theme.json avec des WOFF2 réellement présents, respectent le
 * budget (≤ 4 fichiers chargés par site) et sont acceptées par le validateur.
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Dna\DnaValidator;
use PHPUnit\Framework\TestCase;

/**
 * Complétude et cohérence des paires de polices.
 */
final class FontPairsTest extends TestCase {

	private const THEME = __DIR__ . '/../../themes/maji-framework';

	/**
	 * Charge le catalogue de paires.
	 *
	 * @return array<int, array<string, mixed>> Paires.
	 */
	private function pairs(): array {
		$json  = (string) file_get_contents( self::THEME . '/assets/fonts/fonts.json' );
		$pairs = json_decode( $json, true );
		self::assertIsArray( $pairs );
		return $pairs;
	}

	/**
	 * Slugs des familles enregistrées dans theme.json.
	 *
	 * @return string[] Slugs.
	 */
	private function registered_families(): array {
		$json  = (string) file_get_contents( self::THEME . '/theme.json' );
		$theme = json_decode( $json, true );
		$slugs = [];
		foreach ( $theme['settings']['typography']['fontFamilies'] ?? [] as $family ) {
			$slugs[] = (string) $family['slug'];
		}
		return $slugs;
	}

	/**
	 * La V2-E porte le catalogue à 12 paires, aux identifiants uniques.
	 */
	public function test_twelve_unique_pairs(): void {
		$pairs = $this->pairs();
		self::assertCount( 12, $pairs );

		$ids    = array_column( $pairs, 'id' );
		$combos = array_map(
			static fn( array $p ): string => $p['heading']['slug'] . '+' . $p['body']['slug'],
			$pairs
		);
		self::assertSame( $ids, array_unique( $ids ), 'Identifiants de paires dupliqués.' );
		self::assertSame( $combos, array_unique( $combos ), 'Combinaison heading/body dupliquée.' );
	}

	/**
	 * Chaque paire référence des familles enregistrées, avec WOFF2 présents,
	 * et tient dans le budget de 4 fichiers par site.
	 */
	public function test_pairs_reference_present_fonts_within_budget(): void {
		$registered = $this->registered_families();

		foreach ( $this->pairs() as $pair ) {
			$id    = (string) $pair['id'];
			$files = 0;
			foreach ( [ 'heading', 'body' ] as $role ) {
				$part = $pair[ $role ];
				self::assertContains( $part['slug'], $registered, "$id.$role : famille non enregistrée ({$part['slug']})." );
				foreach ( $part['files'] as $file ) {
					self::assertFileExists( self::THEME . '/assets/fonts/' . $file, "$id : WOFF2 manquant ($file)." );
					++$files;
				}
			}
			self::assertLessThanOrEqual( 4, $files, "$id : budget de 4 fichiers/site dépassé ($files)." );
		}
	}

	/**
	 * Le validateur accepte chacune des 12 paires.
	 */
	public function test_validator_accepts_each_pair(): void {
		$validator = new DnaValidator();
		foreach ( $this->pairs() as $pair ) {
			$fp     = (string) $pair['id'];
			$dna    = [
				'schema'    => 'maji-dna/1',
				'meta'      => [ 'site_slug' => 'x', 'client' => 'X', 'created_at' => '2026-01-01' ],
				'identity'  => [ 'name' => 'X', 'type' => 'hotel', 'phone' => '+22990000000', 'whatsapp' => '+22990000000' ],
				'design'    => [ 'da' => 'da-editorial-sombre', 'font_pair' => $fp ],
				'structure' => [
					'header'         => 'header-02',
					'footer'         => 'footer-01',
					'nav_vocabulary' => 'hotel-classique',
					'pages'          => [ [ 'slug' => 'accueil', 'title' => 'Accueil', 'sections' => [ 'maji/hotel-hero-01' ] ] ],
				],
				'content'   => [],
			];
			$errors = $validator->validate( $dna );
			foreach ( $errors as $error ) {
				self::assertStringNotContainsString( 'font_pair', $error, "$fp inattendu : $error" );
			}
		}
	}
}
