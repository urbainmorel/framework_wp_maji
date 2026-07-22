<?php
/**
 * Commandes `wp maji dna …` (validate, check, register).
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Cli;

use MAJI\Core\Dna\DnaValidator;
use MAJI\Core\Dna\Registry;

/**
 * Validation d'ADN et registre anti-clones.
 */
final class DnaCli {

	/**
	 * Valide un fichier ADN (schéma, références, contrastes WCAG AA).
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : Chemin du fichier dna.json.
	 *
	 * ## EXAMPLES
	 *
	 *     wp maji dna validate dna/examples/hotel-atlantique.json
	 *
	 * @param string[] $args Arguments positionnels.
	 */
	public function validate( array $args ): void {
		$dna = self::load_dna( $args[0] );

		$validator = new DnaValidator( [], self::known_pattern_slugs() );
		$errors    = $validator->validate( $dna );

		if ( [] !== $errors ) {
			foreach ( $errors as $error ) {
				\WP_CLI::warning( $error );
			}
			\WP_CLI::error( sprintf( 'ADN invalide : %d erreur(s).', count( $errors ) ) );
		}
		\WP_CLI::success( 'ADN valide.' );
	}

	/**
	 * Contrôle anti-clones : compare l'ADN au registre des sites livrés.
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : Chemin du fichier dna.json.
	 *
	 * [--registry=<path>]
	 * : Chemin du fichier registry.json. Défaut : registry/registry.json du dépôt.
	 *
	 * [--force]
	 * : Continue malgré une similarité ≥ 0,70 (journalisé).
	 *
	 * ## EXAMPLES
	 *
	 *     wp maji dna check dna/examples/hotel-atlantique.json --registry=registry/registry.json
	 *
	 * @param string[]             $args       Arguments positionnels.
	 * @param array<string, mixed> $assoc_args Options.
	 */
	public function check( array $args, array $assoc_args = [] ): void {
		$dna         = self::load_dna( $args[0] );
		$registry    = (string) ( $assoc_args['registry'] ?? 'registry/registry.json' );
		$force       = isset( $assoc_args['force'] );
		$fingerprint = Registry::fingerprint( $dna );
		$entries     = Registry::read( $registry );

		if ( [] === $entries ) {
			\WP_CLI::success( 'Registre vide : aucun risque de clone.' );
			return;
		}

		$result = Registry::check( $fingerprint, $entries );
		\WP_CLI::log( sprintf( 'Score de similarité maximal : %.2f (seuil : %.2f).', $result['max'], Registry::THRESHOLD ) );

		if ( $result['max'] < Registry::THRESHOLD ) {
			\WP_CLI::success( 'Aucun site trop proche dans le registre.' );
			return;
		}

		$closest = $result['closest'];
		\WP_CLI::warning( sprintf( 'Site le plus proche : %s (%.2f).', (string) ( $closest['site_slug'] ?? '?' ), $result['max'] ) );
		\WP_CLI::warning( 'Axes en collision : ' . implode( ', ', $result['axes'] ) . '.' );
		foreach ( Registry::suggestions( $result['axes'] ) as $suggestion ) {
			\WP_CLI::log( '  → ' . $suggestion );
		}

		if ( $force ) {
			\WP_CLI::warning( 'OPTION --force UTILISÉE : le blocage anti-clones a été contourné (tracé dans cette sortie).' );
			return;
		}
		\WP_CLI::error( 'ADN trop proche d\'un site livré : livraison bloquée.' );
	}

	/**
	 * Ajoute l'empreinte d'un site livré au registre.
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : Chemin du fichier dna.json.
	 *
	 * [--registry=<path>]
	 * : Chemin du fichier registry.json. Défaut : registry/registry.json.
	 *
	 * ## EXAMPLES
	 *
	 *     wp maji dna register dna/examples/hotel-atlantique.json
	 *
	 * @param string[]             $args       Arguments positionnels.
	 * @param array<string, mixed> $assoc_args Options.
	 */
	public function register( array $args, array $assoc_args = [] ): void {
		$dna         = self::load_dna( $args[0] );
		$registry    = (string) ( $assoc_args['registry'] ?? 'registry/registry.json' );
		$fingerprint = Registry::fingerprint( $dna );

		if ( ! Registry::register( $registry, $fingerprint ) ) {
			\WP_CLI::error( sprintf( 'Impossible d\'écrire dans %s.', $registry ) );
		}
		\WP_CLI::success( sprintf( 'Empreinte de « %s » enregistrée dans %s.', (string) $fingerprint['site_slug'], $registry ) );
	}

	/**
	 * Charge et décode un fichier ADN (arrêt en erreur sinon).
	 *
	 * @param string $path Chemin.
	 * @return array<string, mixed> ADN décodé.
	 */
	public static function load_dna( string $path ): array {
		if ( ! file_exists( $path ) ) {
			\WP_CLI::error( sprintf( 'Fichier introuvable : %s.', $path ) );
		}
		$json = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- outillage CLI local.
		$dna  = json_decode( (string) $json, true );
		if ( ! is_array( $dna ) ) {
			\WP_CLI::error( sprintf( 'JSON invalide : %s.', $path ) );
		}
		return $dna;
	}

	/**
	 * Slugs des patterns MAJI enregistrés.
	 *
	 * @return string[] Slugs.
	 */
	public static function known_pattern_slugs(): array {
		$registry = \WP_Block_Patterns_Registry::get_instance();
		$slugs    = [];
		foreach ( $registry->get_all_registered() as $pattern ) {
			$slug = (string) ( $pattern['slug'] ?? $pattern['name'] ?? '' );
			if ( str_starts_with( $slug, 'maji/' ) ) {
				$slugs[] = $slug;
			}
		}
		return $slugs;
	}
}
