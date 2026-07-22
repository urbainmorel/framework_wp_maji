<?php
/**
 * Tests du validateur d'ADN (STI §5.4).
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Dna\DnaValidator;
use PHPUnit\Framework\TestCase;

/**
 * Schéma, références, contrastes, formats.
 */
final class DnaValidatorTest extends TestCase {

	/**
	 * ADN minimal valide.
	 *
	 * @return array<string, mixed> ADN.
	 */
	private function valid_dna(): array {
		$path = __DIR__ . '/../../dna/examples/hotel-atlantique.json';
		$data = json_decode( (string) file_get_contents( $path ), true );
		self::assertIsArray( $data );
		return $data;
	}

	/**
	 * L'exemple de l'Annexe B est valide.
	 */
	public function test_example_dna_is_valid(): void {
		$validator = new DnaValidator();
		self::assertSame( [], $validator->validate( $this->valid_dna() ) );
	}

	/**
	 * Le second exemple (restaurant) est valide.
	 */
	public function test_restaurant_example_is_valid(): void {
		$path = __DIR__ . '/../../dna/examples/saveurs-du-benin.json';
		$data = json_decode( (string) file_get_contents( $path ), true );
		self::assertIsArray( $data );
		$validator = new DnaValidator();
		self::assertSame( [], $validator->validate( $data ) );
	}

	/**
	 * Les 4 ADN des sites modèles sont valides.
	 */
	public function test_model_site_dnas_are_valid(): void {
		$validator = new DnaValidator();
		foreach ( [ 'hotel-business', 'hotel-boutique', 'restaurant-premium', 'restaurant-africain' ] as $seed ) {
			$path = __DIR__ . '/../../demo-content/' . $seed . '/dna.json';
			$data = json_decode( (string) file_get_contents( $path ), true );
			self::assertIsArray( $data, $seed );
			self::assertSame( [], $validator->validate( $data ), $seed . ' doit être valide' );
		}
	}

	/**
	 * Schéma incorrect rejeté.
	 */
	public function test_wrong_schema_rejected(): void {
		$dna           = $this->valid_dna();
		$dna['schema'] = 'maji-dna/2';
		$validator     = new DnaValidator();
		$errors        = $validator->validate( $dna );
		self::assertNotEmpty( $errors );
		self::assertStringContainsString( 'maji-dna/1', $errors[0] );
	}

	/**
	 * DA inconnue rejetée.
	 */
	public function test_unknown_da_rejected(): void {
		$dna                 = $this->valid_dna();
		$dna['design']['da'] = 'da-inexistante';
		$validator           = new DnaValidator();
		$errors              = implode( ' ', $validator->validate( $dna ) );
		self::assertStringContainsString( 'design.da', $errors );
	}

	/**
	 * Téléphone non E.164 rejeté.
	 */
	public function test_invalid_phone_rejected(): void {
		$dna                       = $this->valid_dna();
		$dna['identity']['phone']  = '01 02 03 04';
		$validator                 = new DnaValidator();
		$errors                    = implode( ' ', $validator->validate( $dna ) );
		self::assertStringContainsString( 'E.164', $errors );
	}

	/**
	 * Contraste insuffisant rejeté (F-U6) avec ratio mesuré.
	 */
	public function test_low_contrast_rejected(): void {
		$dna                                        = $this->valid_dna();
		$dna['design']['palette']['primary']          = '#888888';
		$dna['design']['palette']['primary-contrast'] = '#999999';
		$validator                                    = new DnaValidator();
		$errors                                       = implode( ' ', $validator->validate( $dna ) );
		self::assertStringContainsString( 'contraste', $errors );
		self::assertStringContainsString( '4.5', $errors );
	}

	/**
	 * Pattern inconnu rejeté quand le référentiel de slugs est fourni.
	 */
	public function test_unknown_pattern_rejected_with_known_slugs(): void {
		$dna       = $this->valid_dna();
		$validator = new DnaValidator( [], [ 'maji/hotel-hero-03' ] );
		$errors    = implode( ' ', $validator->validate( $dna ) );
		self::assertStringContainsString( 'pattern inconnu', strtolower( $errors ) );
	}

	/**
	 * Vocabulaire inconnu rejeté.
	 */
	public function test_unknown_vocabulary_rejected(): void {
		$dna                                   = $this->valid_dna();
		$dna['structure']['nav_vocabulary']    = 'vocab-inconnu';
		$validator                             = new DnaValidator();
		$errors                                = implode( ' ', $validator->validate( $dna ) );
		self::assertStringContainsString( 'nav_vocabulary', $errors );
	}
}
