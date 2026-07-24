<?php
/**
 * Bootstrap du thème MAJI Framework.
 *
 * Aucune logique métier ici : le thème gère uniquement la présentation.
 *
 * @package maji-framework
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAJI_FRAMEWORK_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/patterns.php';
require_once get_template_directory() . '/inc/fonts.php';
require_once get_template_directory() . '/inc/assets.php';
require_once get_template_directory() . '/inc/motion.php';
require_once get_template_directory() . '/inc/updater.php';
