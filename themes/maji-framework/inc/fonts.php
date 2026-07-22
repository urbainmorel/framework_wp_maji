<?php
/**
 * Catalogue des paires typographiques.
 *
 * Les polices sont déclarées dans theme.json (fontFace) ; ce fichier
 * expose le catalogue de paires (assets/fonts/fonts.json) au reste du
 * système (ADN, CLI) et précharge la police de titres.
 *
 * @package maji-framework
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retourne le catalogue des paires typographiques.
 *
 * @return array<int, array<string, mixed>> Paires du catalogue.
 */
function maji_framework_get_font_pairs(): array {
	static $pairs = null;
	if ( null !== $pairs ) {
		return $pairs;
	}
	$file = get_template_directory() . '/assets/fonts/fonts.json';
	if ( ! file_exists( $file ) ) {
		$pairs = [];
		return $pairs;
	}
	$json  = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fichier local du thème.
	$data  = json_decode( (string) $json, true );
	$pairs = is_array( $data ) ? $data : [];
	return $pairs;
}

/**
 * Retourne une paire typographique par identifiant (fp-01…fp-06).
 *
 * @param string $pair_id Identifiant de paire.
 * @return array<string, mixed>|null Paire ou null si inconnue.
 */
function maji_framework_get_font_pair( string $pair_id ): ?array {
	foreach ( maji_framework_get_font_pairs() as $pair ) {
		if ( isset( $pair['id'] ) && $pair['id'] === $pair_id ) {
			return $pair;
		}
	}
	return null;
}

/**
 * Familles typographiques déclarées, toutes origines confondues (thème + ADN).
 *
 * @return array<int, array<string, mixed>> Familles.
 */
function maji_framework_all_font_families(): array {
	$raw = wp_get_global_settings( [ 'typography', 'fontFamilies' ] );
	if ( ! is_array( $raw ) ) {
		return [];
	}
	// La valeur peut être une liste plate ou séparée par origine (theme/custom/default).
	if ( array_is_list( $raw ) ) {
		return $raw;
	}
	$families = [];
	foreach ( [ 'theme', 'custom', 'default' ] as $origin ) {
		if ( isset( $raw[ $origin ] ) && is_array( $raw[ $origin ] ) ) {
			$families = array_merge( $families, $raw[ $origin ] );
		}
	}
	return $families;
}

/**
 * Slug de la police de titres réellement active (depuis les global styles).
 *
 * Après application d'un ADN, la paire typo choisie fixe la famille des titres :
 * on la lit ici pour précharger la bonne police, pas une police par défaut.
 */
function maji_framework_active_heading_slug(): string {
	$value = wp_get_global_styles( [ 'elements', 'heading', 'typography', 'fontFamily' ] );
	if ( is_string( $value ) && 1 === preg_match( '/font-family--([a-z0-9-]+)/', $value, $m ) ) {
		return $m[1];
	}
	return 'fraunces'; // Police de titres par défaut du thème.
}

/**
 * Précharge le premier fichier WOFF2 de la police de titres active.
 */
function maji_framework_preload_heading_font(): void {
	$slug     = maji_framework_active_heading_slug();
	$families = maji_framework_all_font_families();

	$target = null;
	foreach ( $families as $family ) {
		if ( isset( $family['slug'] ) && $family['slug'] === $slug && ! empty( $family['fontFace'][0]['src'][0] ) ) {
			$target = $family;
			break;
		}
	}
	// Repli : première famille disposant d'un fichier.
	if ( null === $target ) {
		foreach ( $families as $family ) {
			if ( ! empty( $family['fontFace'][0]['src'][0] ) ) {
				$target = $family;
				break;
			}
		}
	}
	if ( null === $target ) {
		return;
	}

	$src = $target['fontFace'][0]['src'][0];
	if ( ! is_string( $src ) ) {
		return;
	}
	$url = str_replace( 'file:./', trailingslashit( get_template_directory_uri() ), $src );
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( $url )
	);
}
add_action( 'wp_head', 'maji_framework_preload_heading_font', 2 );
