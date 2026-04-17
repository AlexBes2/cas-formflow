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

$wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" );

delete_option( 'cas_formflow_db_version' );