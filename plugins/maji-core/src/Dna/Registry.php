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
	 * Pondérations du score (empreinte enrichie V2 — voir docs/DIVERSITE.md §4).
	 *
	 * La « trilogie de première impression » (da + hero + font_pair) somme
	 * exactement au seuil (0.30 + 0.22 + 0.18 = 0.70) : deux sites ne peuvent
	 * pas partager à la fois la même DA, le même hero et la même paire typo.
	 * Les 8 axes secondaires affinent la détection des quasi-clones.
	 *
	 * @var array<string, float>
	 */
	public const WEIGHTS = [
		'da'                     => 0.30,
		'hero'                   => 0.22,
		'font_pair'              => 0.18,
		'palette_hue_bucket'     => 0.06,
		'header'                 => 0.05,
		'footer'                 => 0.04,
		'image_treatment_bucket' => 0.04,
		'section_bg_rhythm'      => 0.03,
		'spacing_mood'           => 0.03,
		'composition_hash'       => 0.03,
		'radius_scale'           => 0.02,
	];

	/**
	 * Seuil de blocage.
	 */
	public const THRESHOLD = 0.70;

	/**
	 * Construit l'empreinte d'un ADN.
	 *
	 * Axes optionnels absents de l'ADN ⇒ chaîne vide : ils ne contribuent
	 * jamais au score (rétrocompatibilité V1). Voir `similarity()`.
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
		$treatment     = is_array( $design['image_treatment'] ?? null ) ? $design['image_treatment'] : [];
		$section_style = (string) ( $design['section_style'] ?? '' );

		return [
			'site_slug'              => (string) ( $dna['meta']['site_slug'] ?? '' ),
			'da'                     => $da,
			'hero'                   => $home_sections[0] ?? '',
			'font_pair'              => (string) ( $design['font_pair'] ?? '' ),
			'palette_hue_bucket'     => self::hue_bucket( $primary ),
			'header'                 => (string) ( $structure['header'] ?? '' ),
			'footer'                 => (string) ( $structure['footer'] ?? '' ),
			'image_treatment_bucket' => self::image_treatment_bucket( $treatment ),
			'section_bg_rhythm'      => (string) ( $design['section_bg_rhythm'] ?? '' ),
			'spacing_mood'           => (string) ( $design['spacing_mood'] ?? '' ),
			'composition_hash'       => self::composition_hash( $home_sections, $section_style ),
			'radius_scale'           => (string) ( $design['radius_scale'] ?? '' ),
			'delivered_at'           => gmdate( 'Y-m-d' ),
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
	 * Bucket de traitement d'image : overlay × duotone × ratio du hero.
	 *
	 * Traitement absent ⇒ chaîne vide (aucun signal, non bloquant).
	 *
	 * @param array<string, mixed> $treatment Bloc design.image_treatment.
	 */
	public static function image_treatment_bucket( array $treatment ): string {
		if ( [] === $treatment ) {
			return '';
		}
		$overlay = (string) ( $treatment['overlay'] ?? 'none' );
		$duotone = empty( $treatment['duotone'] ) ? 'flat' : 'duo';
		$ratio   = (string) ( $treatment['hero_ratio'] ?? '' );
		return implode( ':', [ $overlay, $duotone, $ratio ] );
	}

	/**
	 * Hash de composition : ordre des sections de l'accueil + variante de style.
	 *
	 * Remplace `section_order_hash` (V1) en intégrant `section_style` pour
	 * distinguer deux accueils au même ordre mais au style de section distinct.
	 *
	 * @param string[] $home_sections Sections de l'accueil (dans l'ordre).
	 * @param string   $section_style Variante de style des sections.
	 */
	public static function composition_hash( array $home_sections, string $section_style ): string {
		if ( [] === $home_sections && '' === $section_style ) {
			return '';
		}
		return sha1( implode( '|', $home_sections ) . '#' . $section_style );
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
			'da'                     => 'changez la direction artistique (design.da)',
			'hero'                   => 'changez le hero de l\'accueil (première section)',
			'font_pair'              => 'changez la paire typographique (design.font_pair)',
			'palette_hue_bucket'     => 'changez la teinte de la couleur primaire (design.palette.primary)',
			'header'                 => 'changez la variante d\'en-tête (structure.header)',
			'footer'                 => 'changez la variante de pied de page (structure.footer)',
			'image_treatment_bucket' => 'changez le traitement d\'image (design.image_treatment : overlay, duotone, ratio)',
			'section_bg_rhythm'      => 'changez le rythme des fonds de sections (design.section_bg_rhythm)',
			'spacing_mood'           => 'changez l\'humeur d\'espacement (design.spacing_mood)',
			'composition_hash'       => 'réordonnez les sections de l\'accueil ou changez le style de section (design.section_style)',
			'radius_scale'           => 'changez l\'échelle des rayons (design.radius_scale)',
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
