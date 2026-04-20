<?php
/**
 * Plugin activation logic.
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CAS_FormFlow_Activator {

	/**
	 * Activate plugin.
	 *
	 * @return void
	 */
	public static function activate(): void {
		self::create_tables();
		self::update_db_version_option();
	}

	/**
	 * Create required database tables.
	 *
	 * @return void
	 */
	private static function create_tables(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$tableName      = CAS_FormFlow_Database::get_escaped_submissions_table_name();
		$charsetCollate = $wpdb->get_charset_collate();

		if ( '' === $tableName ) {
			return;
		}

		$sql = "CREATE TABLE {$tableName} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			first_name VARCHAR(191) NOT NULL DEFAULT '',
			phone VARCHAR(50) NOT NULL DEFAULT '',
			email VARCHAR(191) NOT NULL DEFAULT '',
			description TEXT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY email (email),
			KEY created_at (created_at)
		) {$charsetCollate};";

		dbDelta( $sql );
	}

	/**
	 * Store the current DB schema version in the options table.
	 *
	 * @return void
	 */
	private static function update_db_version_option(): void {
		update_option( 'cas_formflow_db_version', CAS_FORMFLOW_DB_VERSION );
	}
}
