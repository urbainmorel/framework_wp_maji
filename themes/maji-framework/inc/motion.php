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
 *
 * Mémoïsé : l'option `maji_dna` n'est pas autoloadée, on évite de la relire à
 * chaque appel (body_class + enqueue) au sein d'une même requête.
 */
function maji_framework_motion_level(): string {
	static $level = null;
	if ( null !== $level ) {
		return $level;
	}

	$dna   = get_option( 'maji_dna' );
	$value = is_array( $dna ) && isset( $dna['design']['motion'] )
		? (string) $dna['design']['motion']
		: 'subtle';

	$level = in_array( $value, maji_framework_motion_levels(), true ) ? $value : 'subtle';
	return $level;
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

	// Ne charger GSAP que sur les vues de contenu susceptibles d'être animées :
	// accueil + contenus singuliers, en excluant la page « politique de
	// confidentialité » (utilitaire, sans animation à piloter).
	$privacy_id = (int) get_option( 'wp_page_for_privacy_policy' );
	$load       = ( is_front_page() || is_singular() ) && ! ( $privacy_id > 0 && is_page( $privacy_id ) );

	/**
	 * Permet d'affiner le chargement de GSAP (ex. exclure d'autres pages).
	 *
	 * @param bool $load Charger GSAP sur la vue courante.
	 */
	if ( ! (bool) apply_filters( 'maji_framework_load_motion', $load ) ) {
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
