<?php
/**
 * Shortcode handler.
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CAS_FormFlow_Shortcode {

	/**
	 * Register shortcode.
	 *
	 * @return void
	 */
	public function register(): void {
		add_shortcode( 'cas_contact_form', array( $this, 'render' ) );
	}

	/**
	 * Render shortcode output.
	 *
	 * @param array<string, mixed> $atts Shortcode attributes.
	 * @return string
	 */
	public function render( array $atts = array() ): string {
		ob_start();

		$template_path = CAS_FORMFLOW_PLUGIN_DIR . 'templates/form.php';

		if ( file_exists( $template_path ) ) {
			include $template_path;
		}

		return (string) ob_get_clean();
	}
}