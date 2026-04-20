<?php
/**
 * Core plugin bootstrap class.
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CAS_FormFlow {

	/**
	 * Single class instance.
	 *
	 * @var CAS_FormFlow|null
	 */
	private static $instance = null;

	/**
	 * Return the singleton instance.
	 *
	 * @return CAS_FormFlow
	 */
	public static function get_instance(): CAS_FormFlow {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register base hooks.
	 *
	 * @return void
	 */
	private function register_hooks(): void {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load translations.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'cas-formflow',
			false,
			dirname( plugin_basename( CAS_FORMFLOW_PLUGIN_FILE ) ) . '/languages/'
		);
	}
}
