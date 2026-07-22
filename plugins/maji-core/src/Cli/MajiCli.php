<?php
/**
 * Commandes `wp maji …` : apply-dna, import-content, export-model, provision.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Cli;

use MAJI\Core\Dna\ContentImporter;
use MAJI\Core\Dna\DnaApplier;
use MAJI\Core\Dna\DnaValidator;
use MAJI\Core\Dna\Registry;
use MAJI\Core\Settings\Settings;

/**
 * Orchestrateur d'usine (STI §6).
 */
final class MajiCli {

	/**
	 * Applique un ADN au site (réglages, global styles, pages, menu).
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : Chemin du fichier dna.json.
	 *
	 * [--registry=<path>]
	 * : Chemin du registre anti-clones. Défaut : registry/registry.json.
	 *
	 * [--skip-registry]
	 * : Ignore le contrôle anti-clones (environnements de dev uniquement).
	 *
	 * ## EXAMPLES
	 *
	 *     wp maji apply-dna dna/examples/hotel-atlantique.json
	 *
	 * @param string[]             $args       Arguments positionnels.
	 * @param array<string, mixed> $assoc_args Options.
	 */
	public function apply_dna( array $args, array $assoc_args = [] ): void {
		$dna = DnaCli::load_dna( $args[0] );

		// 1. Validation.
		$validator = new DnaValidator( [], DnaCli::known_pattern_slugs() );
		$errors    = $validator->validate( $dna );
		if ( [] !== $errors ) {
			foreach ( $errors as $error ) {
				\WP_CLI::warning( $error );
			}
			\WP_CLI::error( 'ADN invalide : application refusée.' );
		}

		// 2. Contrôle registre.
		if ( ! isset( $assoc_args['skip-registry'] ) ) {
			$registry_path = (string) ( $assoc_args['registry'] ?? 'registry/registry.json' );
			$result        = Registry::check( Registry::fingerprint( $dna ), Registry::read( $registry_path ) );
			if ( $result['max'] >= Registry::THRESHOLD ) {
				\WP_CLI::error(
					sprintf(
						'Similarité %.2f ≥ %.2f avec « %s » : exécutez `wp maji dna check` pour le détail, ou --skip-registry en dev.',
						$result['max'],
						Registry::THRESHOLD,
						(string) ( $result['closest']['site_slug'] ?? '?' )
					)
				);
			}
		}

		// 3–5. Application.
		$applier = new DnaApplier( new Settings() );
		$errors  = $applier->apply( $dna );

		if ( [] !== $errors ) {
			foreach ( $errors as $error ) {
				\WP_CLI::warning( $error );
			}
			\WP_CLI::error( 'ADN appliqué avec des erreurs (voir ci-dessus).' );
		}
		\WP_CLI::success( 'ADN appliqué : réglages, styles globaux, pages et navigation en place.' );
	}

	/**
	 * Importe les contenus d'un dossier seed (content.json + media/).
	 *
	 * ## OPTIONS
	 *
	 * <dir>
	 * : Dossier seed (ex. demo-content/hotel-business) ou chemin d'un content.json.
	 *
	 * ## EXAMPLES
	 *
	 *     wp maji import-content demo-content/hotel-business
	 *
	 * @param string[] $args Arguments positionnels.
	 */
	public function import_content( array $args ): void {
		$dir = $args[0];
		if ( is_file( $dir ) ) {
			$dir = dirname( $dir );
		}
		if ( ! is_dir( $dir ) ) {
			\WP_CLI::error( sprintf( 'Dossier introuvable : %s.', $dir ) );
		}

		$importer = new ContentImporter();
		$result   = $importer->import( $dir );

		foreach ( $result['errors'] as $error ) {
			\WP_CLI::warning( $error );
		}
		\WP_CLI::log(
			sprintf(
				'Importé : %d média(s), %d chambre(s), %d plat(s).',
				$result['imported']['media'],
				$result['imported']['rooms'],
				$result['imported']['dishes']
			)
		);

		// Vérification finale : aucun jeton résiduel.
		$residual = $importer->check_residual_tokens();
		if ( [] !== $residual ) {
			foreach ( $residual as $error ) {
				\WP_CLI::warning( $error );
			}
			\WP_CLI::error( 'Des jetons {{maji:*}} restent visibles : import incomplet.' );
		}

		if ( [] !== $result['errors'] ) {
			\WP_CLI::error( 'Import terminé avec des erreurs (voir ci-dessus).' );
		}
		\WP_CLI::success( 'Contenus importés, aucun jeton résiduel.' );
	}

	/**
	 * Exporte le site courant en modèle réutilisable (dossier demo-content/).
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : Nom du modèle (nom du dossier créé).
	 *
	 * [--dest=<dir>]
	 * : Dossier parent de destination. Défaut : demo-content.
	 *
	 * ## EXAMPLES
	 *
	 *     wp maji export-model hotel-business --dest=demo-content
	 *
	 * @param string[]             $args       Arguments positionnels.
	 * @param array<string, mixed> $assoc_args Options.
	 */
	public function export_model( array $args, array $assoc_args = [] ): void {
		$slug = sanitize_title( $args[0] );
		$dest = rtrim( (string) ( $assoc_args['dest'] ?? 'demo-content' ), '/' ) . '/' . $slug;

		if ( ! wp_mkdir_p( $dest ) ) {
			\WP_CLI::error( sprintf( 'Impossible de créer %s.', $dest ) );
		}

		$export = [
			'rooms'  => [],
			'dishes' => [],
			'media'  => [],
		];

		foreach ( get_posts(
			[
				'post_type'      => 'maji_room',
				'posts_per_page' => 100,
			]
		) as $room ) {
			$amenities         = wp_get_object_terms( $room->ID, 'maji_amenity', [ 'fields' => 'names' ] );
			$export['rooms'][] = [
				'title'      => $room->post_title,
				'content'    => $room->post_content,
				'excerpt'    => $room->post_excerpt,
				'price_from' => (int) get_post_meta( $room->ID, 'price_from', true ),
				'capacity'   => (int) get_post_meta( $room->ID, 'capacity', true ),
				'size_sqm'   => (int) get_post_meta( $room->ID, 'size_sqm', true ),
				'featured'   => (bool) get_post_meta( $room->ID, 'featured', true ),
				'amenities'  => is_wp_error( $amenities ) ? [] : $amenities,
			];
		}

		if ( function_exists( 'wc_get_products' ) ) {
			foreach ( wc_get_products( [ 'limit' => 100 ] ) as $product ) {
				$badges             = get_post_meta( $product->get_id(), 'maji_badges', true );
				$categories         = wp_get_object_terms( $product->get_id(), 'product_cat', [ 'fields' => 'names' ] );
				$export['dishes'][] = [
					'name'        => $product->get_name(),
					'description' => $product->get_short_description(),
					'price'       => $product->get_regular_price(),
					'category'    => ! is_wp_error( $categories ) && isset( $categories[0] ) ? $categories[0] : '',
					'badges'      => is_array( $badges ) ? $badges : [],
					'available'   => '0' !== (string) get_post_meta( $product->get_id(), 'maji_available', true ),
				];
			}
		}

		$json = wp_json_encode( $export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		file_put_contents( $dest . '/content.json', (string) $json . "\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- outillage CLI local.
		wp_mkdir_p( $dest . '/media' );

		$dna = get_option( 'maji_dna', null );
		if ( is_array( $dna ) ) {
			file_put_contents( $dest . '/dna.json', (string) wp_json_encode( $dna, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- outillage CLI local.
		}

		\WP_CLI::success( sprintf( 'Modèle exporté dans %s (pensez à copier les médias dans media/).', $dest ) );
	}

	/**
	 * Provision complète : validate → check → apply-dna → import-content → contrôle final.
	 *
	 * Idempotent et relançable sans effet de bord.
	 *
	 * ## OPTIONS
	 *
	 * [--dna=<file>]
	 * : Chemin du fichier dna.json (obligatoire).
	 *
	 * [--model=<dir>]
	 * : Dossier seed à importer. Défaut : content.seed de l'ADN.
	 *
	 * [--with-woo]
	 * : Installe et active WooCommerce si absent (mode restaurant).
	 *
	 * [--registry=<path>]
	 * : Chemin du registre anti-clones.
	 *
	 * [--skip-registry]
	 * : Ignore le contrôle anti-clones (dev uniquement).
	 *
	 * ## EXAMPLES
	 *
	 *     wp maji provision --dna=demo-content/hotel-business/dna.json --with-woo
	 *
	 * @param string[]             $args       Arguments positionnels.
	 * @param array<string, mixed> $assoc_args Options.
	 */
	public function provision( array $args, array $assoc_args = [] ): void {
		unset( $args );
		$dna_path = (string) ( $assoc_args['dna'] ?? '' );
		if ( '' === $dna_path ) {
			\WP_CLI::error( 'Option --dna=<fichier> obligatoire.' );
		}
		$dna = DnaCli::load_dna( $dna_path );

		\WP_CLI::log( '1/5 Validation de l\'ADN…' );
		$validator = new DnaValidator( [], DnaCli::known_pattern_slugs() );
		$errors    = $validator->validate( $dna );
		if ( [] !== $errors ) {
			foreach ( $errors as $error ) {
				\WP_CLI::warning( $error );
			}
			\WP_CLI::error( 'ADN invalide.' );
		}

		\WP_CLI::log( '2/5 Contrôle du registre anti-clones…' );
		if ( ! isset( $assoc_args['skip-registry'] ) ) {
			$registry_path = (string) ( $assoc_args['registry'] ?? 'registry/registry.json' );
			$result        = Registry::check( Registry::fingerprint( $dna ), Registry::read( $registry_path ) );
			if ( $result['max'] >= Registry::THRESHOLD ) {
				\WP_CLI::error( sprintf( 'Similarité %.2f ≥ 0.70 avec « %s » : provision bloquée.', $result['max'], (string) ( $result['closest']['site_slug'] ?? '?' ) ) );
			}
		}

		\WP_CLI::log( '3/5 WooCommerce…' );
		$needs_woo = in_array( $dna['identity']['type'] ?? '', [ 'restaurant', 'mixte' ], true );
		if ( isset( $assoc_args['with-woo'] ) && $needs_woo && ! class_exists( 'WooCommerce' ) ) {
			\WP_CLI::runcommand( 'plugin install woocommerce --activate', [ 'exit_error' => false ] );
			\WP_CLI::runcommand( 'option update woocommerce_currency XOF', [ 'exit_error' => false ] );
			\WP_CLI::runcommand( 'wc payment_gateway update cod --enabled=true --user=1', [ 'exit_error' => false ] );
		}

		\WP_CLI::log( '4/5 Application de l\'ADN…' );
		$applier      = new DnaApplier( new Settings() );
		$apply_errors = $applier->apply( $dna );
		foreach ( $apply_errors as $error ) {
			\WP_CLI::warning( $error );
		}

		\WP_CLI::log( '5/5 Import des contenus…' );
		$seed = (string) ( $assoc_args['model'] ?? ( $dna['content']['seed'] ?? '' ) );
		if ( '' !== $seed && is_dir( $seed ) ) {
			$importer = new ContentImporter();
			$result   = $importer->import( $seed );
			foreach ( $result['errors'] as $error ) {
				\WP_CLI::warning( $error );
			}
			\WP_CLI::log(
				sprintf(
					'Importé : %d média(s), %d chambre(s), %d plat(s).',
					$result['imported']['media'],
					$result['imported']['rooms'],
					$result['imported']['dishes']
				)
			);
			$residual = $importer->check_residual_tokens();
			if ( [] !== $residual ) {
				foreach ( $residual as $error ) {
					\WP_CLI::warning( $error );
				}
				\WP_CLI::error( 'Jetons résiduels détectés : provision incomplète.' );
			}
		} else {
			\WP_CLI::warning( 'Aucun dossier seed : contenus non importés (jetons possiblement résiduels).' );
		}

		if ( [] !== $apply_errors ) {
			\WP_CLI::error( 'Provision terminée avec des erreurs (voir ci-dessus).' );
		}
		\WP_CLI::success( 'Site provisionné. Pensez à `wp maji dna register` après livraison.' );
	}
}
