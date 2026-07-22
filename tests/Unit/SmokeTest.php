<?php
/**
 * Test fumigène : l'autoload PSR-4 du plugin fonctionne.
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Vérifie le câblage de base de la suite de tests.
 */
final class SmokeTest extends TestCase {

	/**
	 * L'autoloader résout les classes du plugin.
	 */
	public function test_plugin_class_is_autoloadable(): void {
		self::assertTrue( class_exists( \MAJI\Core\Plugin::class ) );
	}

	/**
	 * Les stubs WordPress minimaux sont chargés.
	 */
	public function test_wp_stubs_are_loaded(): void {
		self::assertSame( '{"a":1}', wp_json_encode( [ 'a' => 1 ] ) );
	}
}
