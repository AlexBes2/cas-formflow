<?php
/**
 * Database helpers.
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CAS_FormFlow_Database {

	private const SUBMISSIONS_TABLE = 'cas_formflow_submissions';

	/**
	 * Get the submissions table name with the current WordPress prefix.
	 *
	 * @return string
	 */
	public static function get_submissions_table_name(): string {
		global $wpdb;

		return $wpdb->prefix . self::SUBMISSIONS_TABLE;
	}

	/**
	 * Get a backtick-escaped submissions table identifier for SQL fragments.
	 *
	 * WordPress placeholders work with values, not table names. The table name
	 * is validated before being added to raw SQL.
	 *
	 * @return string Empty string when the configured table name is invalid.
	 */
	public static function get_escaped_submissions_table_name(): string {
		$table_name = self::get_submissions_table_name();

		if ( ! self::is_safe_identifier( $table_name ) ) {
			return '';
		}

		return '`' . $table_name . '`';
	}

	/**
	 * Check whether a SQL identifier contains only safe table-name characters.
	 *
	 * @param string $identifier Identifier to validate.
	 * @return bool
	 */
	private static function is_safe_identifier( string $identifier ): bool {
		return 1 === preg_match( '/^[A-Za-z0-9_]+$/', $identifier );
	}
}
