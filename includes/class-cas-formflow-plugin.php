<?php
/**
 * Main plugin class.
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CAS_FormFlow_Plugin {

	/**
	 * Run plugin.
	 *
	 * @return void
	 */
	public function run(): void {
		$this->load_dependencies();
		$this->register_components();
	}

	/**
	 * Load required files.
	 *
	 * @return void
	 */
	private function load_dependencies(): void {
		require_once CAS_FORMFLOW_PLUGIN_DIR . 'includes/class-cas-formflow-shortcode.php';
	}

	/**
	 * Register plugin components.
	 *
	 * @return void
	 */
	private function register_components(): void {
		$shortcode = new CAS_FormFlow_Shortcode();
		$shortcode->register();
	}
}