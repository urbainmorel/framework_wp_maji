<?php
/**
 * Configuration de base du thème.
 *
 * @package maji-framework
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Supports du thème.
 */
function maji_framework_setup(): void {
	load_theme_textdomain( 'maji-framework', get_template_directory() . '/languages' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'maji_framework_setup' );

/**
 * Retire les emojis et autres scripts inutiles pour tenir le budget de performance.
 */
function maji_framework_cleanup(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'maji_framework_cleanup' );
