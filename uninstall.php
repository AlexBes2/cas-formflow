<?php
/**
 * Plugin uninstall cleanup.
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'cas_formflow_submissions';

if ( 1 === preg_match( '/^[A-Za-z0-9_]+$/', $table_name ) ) {
	$wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

delete_option( 'cas_formflow_db_version' );
