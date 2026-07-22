<?php
/**
 * Registre anti-clones (STI §5.3).
 *
 * Classe pure : lecture/écriture de fichier JSON + score de similarité.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Dna;

use MAJI\Core\Support\Wcag;

/**
 * Empreintes des sites livrés et contrôle de similarité bloquant.
 */
final class Registry {

	/**
	 * Pondérations du score (STI §5.3).
	 *
	 * @var array<string, float>
	 */
	public const WEIGHTS = [
		'da'                 => 0.30,
		'font_pair'          => 0.20,
		'hero'               => 0.20,
		'palette_hue_bucket' => 0.15,
		'header'             => 0.10,
		'section_order_hash' => 0.05,
	];

	/**
	 * Seuil de blocage.
	 */
	public const THRESHOLD = 0.70;

	/**
	 * Construit l'empreinte d'un ADN.
	 *
	 * @param array<string, mixed> $dna ADN décodé.
	 * @return array<string, mixed> Empreinte.
	 */
	public static function fingerprint( array $dna ): array {
		$design    = is_array( $dna['design'] ?? null ) ? $dna['design'] : [];
		$structure = is_array( $dna['structure'] ?? null ) ? $dna['structure'] : [];
		$da        = (string) ( $design['da'] ?? '' );

		$palette  = is_array( $design['palette'] ?? null ) ? $design['palette'] : [];
		$resolved = array_merge( DnaValidator::DA_PALETTES[ $da ] ?? [], $palette );
		$primary  = (string) ( $resolved['primary'] ?? '#000000' );

		$home_sections = self::home_sections( $structure );

		return [
			'site_slug'          => (string) ( $dna['meta']['site_slug'] ?? '' ),
			'da'                 => $da,
			'font_pair'          => (string) ( $design['font_pair'] ?? '' ),
			'hero'               => $home_sections[0] ?? '',
			'header'             => (string) ( $structure['header'] ?? '' ),
			'palette_hue_bucket' => self::hue_bucket( $primary ),
			'section_order_hash' => sha1( implode( '|', $home_sections ) ),
			'delivered_at'       => gmdate( 'Y-m-d' ),
		];
	}

	/**
	 * Teinte HSL du primaire quantifiée par tranches de 30° (0–11).
	 *
	 * @param string $hex Couleur hex.
	 */
	public static function hue_bucket( string $hex ): int {
		return (int) floor( Wcag::hue( $hex ) / 30 ) % 12;
	}

	/**
	 * Score de similarité entre deux empreintes (0–1).
	 *
	 * @param array<string, mixed> $a Empreinte A.
	 * @param array<string, mixed> $b Empreinte B.
	 */
	public static function similarity( array $a, array $b ): float {
		$score = 0.0;
		foreach ( self::WEIGHTS as $axis => $weight ) {
			if ( isset( $a[ $axis ], $b[ $axis ] ) && $a[ $axis ] === $b[ $axis ] && '' !== (string) $a[ $axis ] ) {
				$score += $weight;
			}
		}
		return round( $score, 4 );
	}

	/**
	 * Compare une empreinte à toutes celles du registre.
	 *
	 * @param array<string, mixed>             $candidate Empreinte du nouveau site.
	 * @param array<int, array<string, mixed>> $entries   Entrées du registre.
	 * @return array{max: float, closest: array<string, mixed>|null, axes: string[]} Résultat.
	 */
	public static function check( array $candidate, array $entries ): array {
		$max     = 0.0;
		$closest = null;
		foreach ( $entries as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}
			// Un site ne se bloque pas lui-même (re-livraison).
			if ( ( $entry['site_slug'] ?? '' ) === ( $candidate['site_slug'] ?? null ) ) {
				continue;
			}
			$score = self::similarity( $candidate, $entry );
			if ( $score > $max ) {
				$max     = $score;
				$closest = $entry;
			}
		}

		$axes = [];
		if ( null !== $closest ) {
			foreach ( array_keys( self::WEIGHTS ) as $axis ) {
				if ( isset( $candidate[ $axis ], $closest[ $axis ] ) && $candidate[ $axis ] === $closest[ $axis ] && '' !== (string) $candidate[ $axis ] ) {
					$axes[] = $axis;
				}
			}
		}

		return [
			'max'     => $max,
			'closest' => $closest,
			'axes'    => $axes,
		];
	}

	/**
	 * Suggestions d'axes à changer, ordonnées par poids décroissant.
	 *
	 * @param string[] $colliding_axes Axes en collision.
	 * @return string[] Suggestions en français.
	 */
	public static function suggestions( array $colliding_axes ): array {
		$labels = [
			'da'                 => 'changez la direction artistique (design.da)',
			'font_pair'          => 'changez la paire typographique (design.font_pair)',
			'hero'               => 'changez le hero de l\'accueil (première section)',
			'palette_hue_bucket' => 'changez la teinte de la couleur primaire (design.palette.primary)',
			'header'             => 'changez la variante d\'en-tête (structure.header)',
			'section_order_hash' => 'réordonnez ou remplacez les sections de l\'accueil',
		];
		$out    = [];
		foreach ( array_keys( self::WEIGHTS ) as $axis ) {
			if ( in_array( $axis, $colliding_axes, true ) && isset( $labels[ $axis ] ) ) {
				$out[] = $labels[ $axis ];
			}
		}
		return $out;
	}

	/**
	 * Lit un fichier de registre.
	 *
	 * @param string $path Chemin du registry.json.
	 * @return array<int, array<string, mixed>> Entrées.
	 */
	public static function read( string $path ): array {
		if ( ! file_exists( $path ) ) {
			return [];
		}
		$json = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fichier local d'outillage.
		$data = json_decode( (string) $json, true );
		if ( ! is_array( $data ) ) {
			return [];
		}
		$entries = $data['sites'] ?? $data;
		return is_array( $entries ) ? array_values( array_filter( $entries, 'is_array' ) ) : [];
	}

	/**
	 * Ajoute (ou remplace) une empreinte dans le fichier de registre.
	 *
	 * @param string               $path        Chemin du registry.json.
	 * @param array<string, mixed> $fingerprint Empreinte.
	 */
	public static function register( string $path, array $fingerprint ): bool {
		$entries   = self::read( $path );
		$slug      = (string) ( $fingerprint['site_slug'] ?? '' );
		$entries   = array_values(
			array_filter(
				$entries,
				static fn( array $entry ): bool => ( $entry['site_slug'] ?? '' ) !== $slug
			)
		);
		$entries[] = $fingerprint;

		$json = json_encode( [ 'sites' => $entries ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- outillage hors WordPress.
		return false !== file_put_contents( $path, $json . "\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- fichier local d'outillage.
	}

	/**
	 * Sections de la page d'accueil (slug `accueil` ou première page).
	 *
	 * @param array<string, mixed> $structure Structure de l'ADN.
	 * @return string[] Slugs de sections.
	 */
	private static function home_sections( array $structure ): array {
		$pages = is_array( $structure['pages'] ?? null ) ? $structure['pages'] : [];
		$home  = null;
		foreach ( $pages as $page ) {
			if ( is_array( $page ) && 'accueil' === ( $page['slug'] ?? '' ) ) {
				$home = $page;
				break;
			}
		}
		if ( null === $home && isset( $pages[0] ) && is_array( $pages[0] ) ) {
			$home = $pages[0];
		}
		$sections = is_array( $home['sections'] ?? null ) ? $home['sections'] : [];
		return array_map( 'strval', $sections );
	}
}
