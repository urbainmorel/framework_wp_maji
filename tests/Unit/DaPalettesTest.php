<?php
/**
 * Garde-fou des directions artistiques (V2-C).
 *
 * Vérifie que les 10 DA sont complètes, validées WCAG AA sur toutes les paires
 * critiques (y compris le rythme de fonds base/surface/surface-alt de V2-B),
 * cohérentes avec les fichiers de style variation, et acceptées par le validateur.
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Dna\DnaValidator;
use MAJI\Core\Support\Wcag;
use PHPUnit\Framework\TestCase;

/**
 * Complétude, contrastes et cohérence des DA.
 */
final class DaPalettesTest extends TestCase {

	private const STYLES_DIR = __DIR__ . '/../../themes/maji-framework/styles';

	private const SLUGS = [
		'base',
		'contrast',
		'primary',
		'primary-contrast',
		'accent',
		'accent-contrast',
		'surface',
		'surface-alt',
		'ink',
		'ink-muted',
	];

	/**
	 * Paires de contraste à garantir (avant-plan / arrière-plan).
	 * Couvre le texte courant sur les 3 fonds neutres (rythme V2-B), les titres
	 * et les paires bouton/couleur.
	 *
	 * @var array<int, array{0: string, 1: string}>
	 */
	private const PAIRS = [
		[ 'ink', 'base' ],
		[ 'ink', 'surface' ],
		[ 'ink', 'surface-alt' ],
		[ 'contrast', 'base' ],
		[ 'primary-contrast', 'primary' ],
		[ 'accent-contrast', 'accent' ],
	];

	/**
	 * La V2-C porte le catalogue à 10 DA.
	 */
	public function test_ten_directions(): void {
		self::assertCount( 10, DnaValidator::DA_PALETTES );
	}

	/**
	 * Fournit chaque slug de DA.
	 *
	 * @return array<string, array{0: string}>
	 */
	public static function da_provider(): array {
		$cases = [];
		foreach ( array_keys( DnaValidator::DA_PALETTES ) as $da ) {
			$cases[ $da ] = [ $da ];
		}
		return $cases;
	}

	/**
	 * Chaque DA est complète (10 slugs hex) et passe WCAG AA sur toutes les paires.
	 *
	 * @dataProvider da_provider
	 *
	 * @param string $da Slug de la DA.
	 */
	public function test_palette_complete_and_wcag( string $da ): void {
		$palette = DnaValidator::DA_PALETTES[ $da ];

		foreach ( self::SLUGS as $slug ) {
			self::assertArrayHasKey( $slug, $palette, "$da : slug manquant $slug" );
			self::assertMatchesRegularExpression( '/^#[0-9A-Fa-f]{6}$/', $palette[ $slug ], "$da.$slug" );
		}

		foreach ( self::PAIRS as [$fg, $bg] ) {
			$ratio = Wcag::contrast_ratio( $palette[ $fg ], $palette[ $bg ] );
			self::assertGreaterThanOrEqual(
				Wcag::AA_NORMAL,
				$ratio,
				sprintf( '%s : contraste %s/%s insuffisant (%.2f).', $da, $fg, $bg, $ratio )
			);
		}

		self::assertFileExists( self::STYLES_DIR . "/$da.json", "$da : fichier de style variation manquant" );
	}

	/**
	 * Les fichiers de style variation correspondent exactement aux palettes.
	 */
	public function test_style_files_match_palettes(): void {
		$files      = glob( self::STYLES_DIR . '/da-*.json' ) ?: [];
		$file_slugs = array_map( static fn( string $f ): string => basename( $f, '.json' ), $files );
		sort( $file_slugs );

		$palette_slugs = array_keys( DnaValidator::DA_PALETTES );
		sort( $palette_slugs );

		self::assertSame( $palette_slugs, $file_slugs );
	}

	/**
	 * Le validateur accepte chaque DA (référencée et contrastes OK).
	 */
	public function test_validator_accepts_each_da(): void {
		$validator = new DnaValidator();
		foreach ( array_keys( DnaValidator::DA_PALETTES ) as $da ) {
			$dna    = [
				'schema'    => 'maji-dna/1',
				'meta'      => [ 'site_slug' => 'x', 'client' => 'X', 'created_at' => '2026-01-01' ],
				'identity'  => [ 'name' => 'X', 'type' => 'hotel', 'phone' => '+22990000000', 'whatsapp' => '+22990000000' ],
				'design'    => [ 'da' => $da, 'font_pair' => 'fp-01' ],
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
				self::assertStringNotContainsString( 'design.da', $error, "$da inattendu : $error" );
				self::assertStringNotContainsString( 'contraste', $error, "$da inattendu : $error" );
			}
		}
	}
}
