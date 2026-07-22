<?php
/**
 * Catégories de patterns MAJI.
 *
 * Les patterns eux-mêmes sont chargés automatiquement par WordPress
 * depuis le dossier patterns/ (en-têtes de fichiers standard).
 *
 * @package maji-framework
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Déclare les catégories de patterns de la bibliothèque MAJI.
 */
function maji_framework_register_pattern_categories(): void {
	register_block_pattern_category(
		'maji-hotel',
		[ 'label' => __( 'MAJI — Hôtel', 'maji-framework' ) ]
	);
	register_block_pattern_category(
		'maji-restaurant',
		[ 'label' => __( 'MAJI — Restaurant', 'maji-framework' ) ]
	);
	register_block_pattern_category(
		'maji-commun',
		[ 'label' => __( 'MAJI — Commun', 'maji-framework' ) ]
	);
}
add_action( 'init', 'maji_framework_register_pattern_categories', 9 );
