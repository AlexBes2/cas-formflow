<?php
/**
 * AJAX request handlers.
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CAS_FormFlow_Ajax {

	private const ACTION = 'cas_formflow_submit';
	private const NONCE_ACTION = 'cas_formflow_submit';
	private const NONCE_FIELD = 'nonce';

	/**
	 * Register AJAX hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'wp_ajax_' . self::ACTION, array( $this, 'handle_submit' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, array( $this, 'handle_submit' ) );
	}

	/**
	 * Returns AJAX config for frontend scripts.
	 *
	 * @return array<string, string>
	 */
	public static function get_frontend_config(): array {
		return array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'action'  => self::ACTION,
			'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
		);
	}

	/**
	 * Handle form submission.
	 *
	 * @return void
	 */
	public function handle_submit(): void {
		if ( ! check_ajax_referer( self::NONCE_ACTION, self::NONCE_FIELD, false ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security check failed. Refresh the page and try again.', 'cas-formflow' ),
				),
				403
			);
		}

		$data   = $this->sanitize_submission( wp_unslash( $_POST ) );
		$errors = $this->validate_submission( $data );

		if ( ! empty( $errors ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please fix the highlighted fields before submitting.', 'cas-formflow' ),
					'errors'  => $errors,
				),
				422
			);
		}

		global $wpdb;

		$table_name  = $wpdb->prefix . 'cas_formflow_submissions';
		$description = wp_json_encode(
			array(
				'last_name'      => $data['last_name'],
				'date_of_birth'  => $data['date_of_birth'],
				'country'        => $data['country'],
				'city'           => $data['city'],
				'street_address' => $data['street_address'],
				'postal_code'    => $data['postal_code'],
				'terms'          => $data['terms'],
				'newsletter'     => $data['newsletter'],
			)
		);

		if ( false === $description ) {
			$description = '';
		}

		$inserted = $wpdb->insert(
			$table_name,
			array(
				'first_name'  => $data['first_name'],
				'phone'       => $data['phone'],
				'email'       => $data['email'],
				'description' => $description,
				'created_at'  => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s', '%s' )
		);

		if ( false === $inserted ) {
			wp_send_json_error(
				array(
					'message' => __( 'Could not save your submission. Please try again.', 'cas-formflow' ),
				),
				500
			);
		}

		wp_send_json_success(
			array(
				'message' => __( 'Thank you. Your submission has been received.', 'cas-formflow' ),
			)
		);
	}

	/**
	 * Sanitize request payload.
	 *
	 * @param array<string, mixed> $input Raw request payload.
	 * @return array<string, mixed>
	 */
	private function sanitize_submission( array $input ): array {
		return array(
			'first_name'     => sanitize_text_field( $this->get_raw_input( $input, 'first_name' ) ),
			'last_name'      => sanitize_text_field( $this->get_raw_input( $input, 'last_name' ) ),
			'email'          => sanitize_email( $this->get_raw_input( $input, 'email' ) ),
			'phone'          => sanitize_text_field( $this->get_raw_input( $input, 'phone' ) ),
			'date_of_birth'  => sanitize_text_field( $this->get_raw_input( $input, 'date_of_birth' ) ),
			'country'        => sanitize_text_field( $this->get_raw_input( $input, 'country' ) ),
			'city'           => sanitize_text_field( $this->get_raw_input( $input, 'city' ) ),
			'street_address' => sanitize_text_field( $this->get_raw_input( $input, 'street_address' ) ),
			'postal_code'    => sanitize_text_field( $this->get_raw_input( $input, 'postal_code' ) ),
			'terms'          => ! empty( $input['terms'] ) ? '1' : '',
			'newsletter'     => ! empty( $input['newsletter'] ) ? '1' : '',
		);
	}

	/**
	 * Get a scalar request value.
	 *
	 * @param array<string, mixed> $input Request payload.
	 * @param string               $key Payload key.
	 * @return string
	 */
	private function get_raw_input( array $input, string $key ): string {
		if ( ! isset( $input[ $key ] ) || is_array( $input[ $key ] ) ) {
			return '';
		}

		return (string) $input[ $key ];
	}

	/**
	 * Validate sanitized payload.
	 *
	 * @param array<string, mixed> $data Sanitized payload.
	 * @return array<string, string>
	 */
	private function validate_submission( array $data ): array {
		$errors = array();

		foreach ( array( 'first_name', 'last_name', 'phone', 'country', 'city' ) as $field ) {
			if ( '' === $data[ $field ] ) {
				$errors[ $field ] = __( 'This field is required.', 'cas-formflow' );
			}
		}

		if ( '' === $data['email'] || ! is_email( $data['email'] ) ) {
			$errors['email'] = __( 'Enter a valid email address.', 'cas-formflow' );
		}

		if ( '' === $data['terms'] ) {
			$errors['terms'] = __( 'Accept the Terms and Conditions to continue.', 'cas-formflow' );
		}

		if ( '' !== $data['phone'] && ! preg_match( '/^[0-9+() .-]{7,20}$/', (string) $data['phone'] ) ) {
			$errors['phone'] = __( 'Use 7-20 digits and phone symbols only.', 'cas-formflow' );
		}

		return $errors;
	}
}
