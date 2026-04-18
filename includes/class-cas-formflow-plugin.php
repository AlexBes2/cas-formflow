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
		$this->register_core();
		$this->register_components();
		$this->register_hooks();
	}

	/**
	 * Load required files.
	 *
	 * @return void
	 */
	private function load_dependencies(): void {
		require_once CAS_FORMFLOW_PLUGIN_DIR . 'includes/class-cas-formflow.php';
		require_once CAS_FORMFLOW_PLUGIN_DIR . 'includes/class-cas-formflow-admin.php';
		require_once CAS_FORMFLOW_PLUGIN_DIR . 'includes/class-cas-formflow-ajax.php';
		require_once CAS_FORMFLOW_PLUGIN_DIR . 'includes/class-cas-formflow-mailer.php';
		require_once CAS_FORMFLOW_PLUGIN_DIR . 'includes/class-cas-formflow-shortcode.php';
	}

	/**
	 * Register core bootstrap.
	 *
	 * @return void
	 */
	private function register_core(): void {
		CAS_FormFlow::get_instance();
	}

	/**
	 * Register plugin components.
	 *
	 * @return void
	 */
	private function register_components(): void {
		$admin = new CAS_FormFlow_Admin();
		$admin->register();

		$ajax = new CAS_FormFlow_Ajax();
		$ajax->register();

		$mailer = new CAS_FormFlow_Mailer();
		$mailer->register();

		$shortcode = new CAS_FormFlow_Shortcode();
		$shortcode->register();
	}

	/**
	 * Register plugin hooks.
	 *
	 * @return void
	 */
	private function register_hooks(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue frontend assets only on pages that contain plugin shortcode.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		if ( ! is_singular() ) {
			return;
		}

		global $post;

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		if ( ! has_shortcode( $post->post_content, 'cas_contact_form' ) ) {
			return;
		}

		wp_enqueue_style(
			'cas-formflow-bootstrap',
			CAS_FORMFLOW_PLUGIN_URL . 'assets/css/bootstrap.min.css',
			array(),
			'5.3.8'
		);

		wp_enqueue_style(
			'cas-formflow-style',
			CAS_FORMFLOW_PLUGIN_URL . 'assets/css/style.css',
			array( 'cas-formflow-bootstrap' ),
			CAS_FORMFLOW_VERSION
		);

		wp_enqueue_script(
			'cas-formflow-script',
			CAS_FORMFLOW_PLUGIN_URL . 'assets/js/formflow.js',
			array(),
			CAS_FORMFLOW_VERSION,
			true
		);

		wp_localize_script(
			'cas-formflow-script',
			'casFormflow',
			CAS_FormFlow_Ajax::get_frontend_config()
		);
	}
}
