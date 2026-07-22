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
 * Précharge le premier fichier WOFF2 de la police de titres active.
 */
function maji_framework_preload_heading_font(): void {
	$settings = wp_get_global_settings( [ 'typography', 'fontFamilies' ] );
	if ( ! is_array( $settings ) ) {
		return;
	}
	$families = $settings['theme'] ?? [];
	foreach ( $families as $family ) {
		if ( empty( $family['fontFace'][0]['src'][0] ) ) {
			continue;
		}
		$src = $family['fontFace'][0]['src'][0];
		if ( ! is_string( $src ) ) {
			continue;
		}
		$url = str_replace( 'file:./', trailingslashit( get_template_directory_uri() ), $src );
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( $url )
		);
		break;
	}
}
add_action( 'wp_head', 'maji_framework_preload_heading_font', 2 );
