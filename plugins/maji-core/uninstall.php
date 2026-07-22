<?php
/**
 * Désinstallation du plugin : suppression des options et tables MAJI.
 *
 * Les contenus (réservations, chambres) sont conservés : ce sont des
 * données client, leur suppression est une décision manuelle.
 *
 * @package maji-core
 */

declare(strict_types=1);

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'maji_settings' );
delete_option( 'maji_webhook_secret' );
delete_option( 'maji_dna' );

$maji_role = get_role( 'administrator' );
if ( null !== $maji_role ) {
	$maji_role->remove_cap( 'manage_maji' );
}

global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}maji_webhook_log" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- désinstallation.
