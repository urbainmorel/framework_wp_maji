<?php
/**
 * Bootstrap PHPUnit — tests unitaires purs (sans WordPress).
 *
 * Les classes testées ici (validateur d'ADN, similarité du registre,
 * constructeurs de payloads, jetons) sont conçues sans dépendance WP ;
 * les quelques fonctions WordPress utilisées sont polyfillées ci-dessous.
 *
 * @package maji
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Autoload PSR-4 du plugin sans passer par Composer du plugin.
spl_autoload_register(
	static function ( string $class ): void {
		if ( ! str_starts_with( $class, 'MAJI\\Core\\' ) ) {
			return;
		}
		$relative = substr( $class, strlen( 'MAJI\\Core\\' ) );
		$path     = __DIR__ . '/../plugins/maji-core/src/' . str_replace( '\\', '/', $relative ) . '.php';
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
);

require_once __DIR__ . '/wp-stubs.php';
