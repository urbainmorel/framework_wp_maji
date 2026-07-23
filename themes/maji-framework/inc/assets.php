<?php
/**
 * Chargement des assets front du thème.
 *
 * @package maji-framework
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Feuille de style principale (en-tête du thème uniquement, les tokens
 * viennent de theme.json).
 */
function maji_framework_enqueue_assets(): void {
	wp_enqueue_style(
		'maji-framework',
		get_stylesheet_uri(),
		[],
		MAJI_FRAMEWORK_VERSION
	);
	wp_enqueue_style(
		'maji-framework-structural',
		get_template_directory_uri() . '/assets/css/maji.css',
		[],
		MAJI_FRAMEWORK_VERSION
	);
	// Motion premium (V2-G) : animations natives, tokens only, coupées sous
	// prefers-reduced-motion et sans état caché quand non supporté.
	wp_enqueue_style(
		'maji-framework-motion',
		get_template_directory_uri() . '/assets/css/motion.css',
		[ 'maji-framework-structural' ],
		MAJI_FRAMEWORK_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'maji_framework_enqueue_assets' );
