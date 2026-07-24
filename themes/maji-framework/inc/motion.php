<?php
/**
 * Motion premium — pilotage par l'ADN (V2-G2).
 *
 * Le niveau `design.motion` de l'ADN (stocké dans l'option `maji_dna`) pilote :
 *  - une classe `maji-motion-{niveau}` sur <body> (le CSS de G1 s'y adosse) ;
 *  - le chargement CONDITIONNEL de GSAP + ScrollTrigger (auto-hébergés, jamais
 *    en CDN), en `defer`, UNIQUEMENT au niveau `expressive`.
 *
 * @package maji-framework
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Niveaux de motion reconnus.
 *
 * @return string[] Niveaux valides.
 */
function maji_framework_motion_levels(): array {
	return [ 'none', 'subtle', 'standard', 'expressive' ];
}

/**
 * Niveau de motion appliqué au site (défaut : subtle).
 */
function maji_framework_motion_level(): string {
	$dna   = get_option( 'maji_dna' );
	$level = is_array( $dna ) && isset( $dna['design']['motion'] )
		? (string) $dna['design']['motion']
		: 'subtle';

	return in_array( $level, maji_framework_motion_levels(), true ) ? $level : 'subtle';
}

/**
 * Ajoute la classe de niveau de motion au <body>.
 *
 * @param string[] $classes Classes existantes.
 * @return string[] Classes.
 */
function maji_framework_motion_body_class( array $classes ): array {
	$classes[] = 'maji-motion-' . maji_framework_motion_level();
	return $classes;
}
add_filter( 'body_class', 'maji_framework_motion_body_class' );

/**
 * Charge GSAP + le module d'init au niveau `expressive` uniquement.
 *
 * GSAP est auto-hébergé (assets/js/vendor/gsap), chargé en `defer` et hors
 * admin/éditeur. Les autres niveaux restent à 0 Ko de JS (socle CSS de G1).
 */
function maji_framework_motion_enqueue(): void {
	if ( is_admin() || 'expressive' !== maji_framework_motion_level() ) {
		return;
	}

	$base = get_template_directory_uri() . '/assets/js/vendor/gsap/';
	$args = [
		'in_footer' => true,
		'strategy'  => 'defer',
	];

	wp_enqueue_script( 'gsap', $base . 'gsap.min.js', [], '3.15.0', $args );
	wp_enqueue_script( 'gsap-scrolltrigger', $base . 'ScrollTrigger.min.js', [ 'gsap' ], '3.15.0', $args );
	wp_enqueue_script(
		'maji-framework-motion',
		get_template_directory_uri() . '/assets/js/motion-gsap.js',
		[ 'gsap', 'gsap-scrolltrigger' ],
		MAJI_FRAMEWORK_VERSION,
		$args
	);
}
add_action( 'wp_enqueue_scripts', 'maji_framework_motion_enqueue' );
