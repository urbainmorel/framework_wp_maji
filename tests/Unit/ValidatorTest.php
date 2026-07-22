<?php
/**
 * Tests du validateur partagé.
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Support\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Validations E.164, dates, heures, devises, couleurs.
 */
final class ValidatorTest extends TestCase {

	/**
	 * Numéros E.164 valides.
	 */
	public function test_valid_e164(): void {
		self::assertTrue( Validator::is_e164( '+22901020304' ) );
		self::assertTrue( Validator::is_e164( '+2250701020304' ) );
		self::assertTrue( Validator::is_e164( '+33612345678' ) );
	}

	/**
	 * Numéros invalides rejetés.
	 */
	public function test_invalid_e164(): void {
		self::assertFalse( Validator::is_e164( '0701020304' ) );
		self::assertFalse( Validator::is_e164( '+0122' ) );
		self::assertFalse( Validator::is_e164( '+229 01 02 03 04' ) );
		self::assertFalse( Validator::is_e164( '' ) );
	}

	/**
	 * Dates au format Y-m-d.
	 */
	public function test_dates(): void {
		self::assertTrue( Validator::is_date( '2026-08-02' ) );
		self::assertFalse( Validator::is_date( '2026-13-02' ) );
		self::assertFalse( Validator::is_date( '02/08/2026' ) );
	}

	/**
	 * Cohérence des plages de dates.
	 */
	public function test_date_range(): void {
		self::assertTrue( Validator::is_date_range( '2026-08-02', '2026-08-05' ) );
		self::assertFalse( Validator::is_date_range( '2026-08-05', '2026-08-02' ) );
		self::assertFalse( Validator::is_date_range( '2026-08-02', '2026-08-02' ) );
	}

	/**
	 * Heures, devises et couleurs.
	 */
	public function test_misc(): void {
		self::assertTrue( Validator::is_time( '08:30' ) );
		self::assertFalse( Validator::is_time( '24:00' ) );
		self::assertTrue( Validator::is_currency( 'XOF' ) );
		self::assertFalse( Validator::is_currency( 'CFA francs' ) );
		self::assertTrue( Validator::is_hex_color( '#C9A227' ) );
		self::assertTrue( Validator::is_hex_color( '#fff' ) );
		self::assertFalse( Validator::is_hex_color( 'C9A227' ) );
	}
}
