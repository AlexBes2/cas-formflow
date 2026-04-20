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
	private const REQUIRED_FIELDS = array(
		'first_name',
		'last_name',
		'email',
		'phone',
		'country',
		'city',
		'terms',
	);

	private const FIELD_MAX_LENGTHS = array(
		'first_name'     => 191,
		'last_name'      => 191,
		'email'          => 191,
		'phone'          => 50,
		'date_of_birth'  => 10,
		'country'        => 100,
		'city'           => 100,
		'street_address' => 191,
		'postal_code'    => 20,
	);

	private const ALLOWED_COUNTRIES = array(
		'Ukraine',
		'United States',
		'United Kingdom',
		'Germany',
		'Poland',
	);

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
	 * Return AJAX config for frontend scripts.
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

		$data = $this->sanitize_submission(
			wp_unslash( $_POST ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		);
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

		$submission_id = $this->save_submission( $data );

		if ( 0 === $submission_id ) {
			wp_send_json_error(
				array(
					'message' => __( 'Could not save your submission. Please try again.', 'cas-formflow' ),
				),
				500
			);
		}

		$this->send_admin_notification( $submission_id, $data );

		wp_send_json_success(
			array(
				'message'      => __( 'Thank you. Your submission has been received.', 'cas-formflow' ),
				'submissionId' => $submission_id,
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
			'email'          => strtolower( sanitize_email( $this->get_raw_input( $input, 'email' ) ) ),
			'phone'          => sanitize_text_field( $this->get_raw_input( $input, 'phone' ) ),
			'date_of_birth'  => sanitize_text_field( $this->get_raw_input( $input, 'date_of_birth' ) ),
			'country'        => sanitize_text_field( $this->get_raw_input( $input, 'country' ) ),
			'city'           => sanitize_text_field( $this->get_raw_input( $input, 'city' ) ),
			'street_address' => sanitize_text_field( $this->get_raw_input( $input, 'street_address' ) ),
			'postal_code'    => sanitize_text_field( $this->get_raw_input( $input, 'postal_code' ) ),
			'terms'          => $this->get_checkbox_value( $input, 'terms' ),
			'newsletter'     => $this->get_checkbox_value( $input, 'newsletter' ),
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

		return trim( (string) $input[ $key ] );
	}

	/**
	 * Get a normalized checkbox value.
	 *
	 * @param array<string, mixed> $input Request payload.
	 * @param string               $key Payload key.
	 * @return string
	 */
	private function get_checkbox_value( array $input, string $key ): string {
		if ( ! isset( $input[ $key ] ) || is_array( $input[ $key ] ) ) {
			return '';
		}

		return '1' === (string) $input[ $key ] ? '1' : '';
	}

	/**
	 * Validate sanitized payload.
	 *
	 * @param array<string, mixed> $data Sanitized payload.
	 * @return array<string, string>
	 */
	private function validate_submission( array $data ): array {
		$errors = array();

		foreach ( self::REQUIRED_FIELDS as $field ) {
			if ( empty( $data[ $field ] ) ) {
				$errors[ $field ] = __( 'This field is required.', 'cas-formflow' );
			}
		}

		foreach ( self::FIELD_MAX_LENGTHS as $field => $max_length ) {
			if ( ! empty( $data[ $field ] ) && $this->get_text_length( (string) $data[ $field ] ) > $max_length ) {
				$errors[ $field ] = sprintf(
					/* translators: %d: maximum number of characters. */
					__( 'Use %d characters or fewer.', 'cas-formflow' ),
					$max_length
				);
			}
		}

		if ( '' !== $data['email'] && ! is_email( $data['email'] ) ) {
			$errors['email'] = __( 'Enter a valid email address.', 'cas-formflow' );
		}

		if ( '' !== $data['country'] && ! in_array( $data['country'], self::ALLOWED_COUNTRIES, true ) ) {
			$errors['country'] = __( 'Select a valid country.', 'cas-formflow' );
		}

		if ( '' !== $data['date_of_birth'] ) {
			$date_of_birth = (string) $data['date_of_birth'];

			if ( ! $this->is_valid_date_of_birth( $date_of_birth ) ) {
				$errors['date_of_birth'] = __( 'Enter a valid date of birth.', 'cas-formflow' );
			} elseif ( $date_of_birth > $this->get_max_date_of_birth() ) {
				$errors['date_of_birth'] = sprintf(
					/* translators: %d: minimum age in years. */
					__( 'You must be at least %d years old.', 'cas-formflow' ),
					CAS_FORMFLOW_MIN_AGE
				);
			} elseif ( $date_of_birth < CAS_FORMFLOW_MIN_DATE_OF_BIRTH ) {
				$errors['date_of_birth'] = __( 'Enter a valid date of birth.', 'cas-formflow' );
			}
		}

		if ( empty( $data['terms'] ) ) {
			$errors['terms'] = __( 'Accept the Terms and Conditions to continue.', 'cas-formflow' );
		}

		if ( '' !== $data['phone'] && ! preg_match( '/^[0-9+() .-]{7,20}$/', (string) $data['phone'] ) ) {
			$errors['phone'] = __( 'Use 7-20 digits and phone symbols only.', 'cas-formflow' );
		}

		return $errors;
	}

	/**
	 * Get text length in characters when multibyte support is available.
	 *
	 * @param string $value Text value.
	 * @return int
	 */
	private function get_text_length( string $value ): int {
		if ( function_exists( 'mb_strlen' ) ) {
			return mb_strlen( $value );
		}

		return strlen( $value );
	}

	/**
	 * Check date of birth format and range.
	 *
	 * @param string $date Date value in YYYY-MM-DD format.
	 * @return bool
	 */
	private function is_valid_date_of_birth( string $date ): bool {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			return false;
		}

		$parts = explode( '-', $date );
		$year  = (int) $parts[0];
		$month = (int) $parts[1];
		$day   = (int) $parts[2];

		if ( ! checkdate( $month, $day, $year ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get latest allowed date of birth for the configured minimum age.
	 *
	 * @return string Date in YYYY-MM-DD format.
	 */
	private function get_max_date_of_birth(): string {
		return wp_date(
			'Y-m-d',
			strtotime( '-' . CAS_FORMFLOW_MIN_AGE . ' years', current_time( 'timestamp' ) )
		);
	}

	/**
	 * Persist a validated submission.
	 *
	 * @param array<string, mixed> $data Sanitized and validated payload.
	 * @return int New submission ID, or 0 on failure.
	 */
	private function save_submission( array $data ): int {
		global $wpdb;

		$inserted = $wpdb->insert(
			CAS_FormFlow_Database::get_submissions_table_name(),
			$this->prepare_submission_row( $data ),
			array( '%s', '%s', '%s', '%s', '%s' )
		);

		if ( false === $inserted ) {
			return 0;
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Prepare DB row values from sanitized payload.
	 *
	 * @param array<string, mixed> $data Sanitized and validated payload.
	 * @return array<string, string>
	 */
	private function prepare_submission_row( array $data ): array {
		return array(
			'first_name'  => (string) $data['first_name'],
			'phone'       => (string) $data['phone'],
			'email'       => (string) $data['email'],
			'description' => $this->prepare_submission_description( $data ),
			'created_at'  => current_time( 'mysql' ),
		);
	}

	/**
	 * Prepare JSON description for fields without dedicated columns.
	 *
	 * @param array<string, mixed> $data Sanitized and validated payload.
	 * @return string
	 */
	private function prepare_submission_description( array $data ): string {
		$description = wp_json_encode(
			array(
				'last_name'      => $data['last_name'],
				'date_of_birth'  => $data['date_of_birth'],
				'country'        => $data['country'],
				'city'           => $data['city'],
				'street_address' => $data['street_address'],
				'postal_code'    => $data['postal_code'],
				'terms'          => '1' === $data['terms'],
				'newsletter'     => '1' === $data['newsletter'],
			)
		);

		if ( false === $description ) {
			return '';
		}

		return $description;
	}

	/**
	 * Notify site admin about a new saved submission.
	 *
	 * Email delivery failures are logged but do not block a saved submission.
	 *
	 * @param int                  $submission_id Saved submission ID.
	 * @param array<string, mixed> $data Sanitized and validated payload.
	 * @return bool Whether WordPress accepted the email for delivery.
	 */
	private function send_admin_notification( int $submission_id, array $data ): bool {
		$recipients = (array) apply_filters(
			'cas_formflow_admin_notification_recipients',
			$this->get_admin_notification_recipients(),
			$submission_id,
			$data
		);
		$recipients = $this->sanitize_notification_recipients( $recipients );

		if ( empty( $recipients ) ) {
			return false;
		}

		$site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$subject   = (string) apply_filters(
			'cas_formflow_admin_notification_subject',
			sprintf(
				/* translators: %s: site name. */
				__( '[%s] New CAS FormFlow submission', 'cas-formflow' ),
				$site_name
			),
			$submission_id,
			$data
		);
		$subject   = sanitize_text_field( $subject );

		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
		);

		if ( is_email( (string) $data['email'] ) ) {
			$headers[] = 'Reply-To: ' . (string) $data['email'];
		}

		$sent = wp_mail(
			$recipients,
			$subject,
			$this->prepare_admin_notification_message( $submission_id, $data ),
			$headers
		);

		if ( ! $sent && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				sprintf(
					'CAS FormFlow: admin notification email failed for submission ID %d.',
					$submission_id
				)
			);
		}

		return $sent;
	}

	/**
	 * Get default admin notification recipients.
	 *
	 * @return array<int, string>
	 */
	private function get_admin_notification_recipients(): array {
		$recipients = array(
			get_option( 'admin_email' ),
		);

		$admin_users = get_users(
			array(
				'role'   => 'administrator',
				'fields' => array( 'user_email' ),
			)
		);

		foreach ( $admin_users as $admin_user ) {
			if ( isset( $admin_user->user_email ) ) {
				$recipients[] = $admin_user->user_email;
			}
		}

		return $recipients;
	}

	/**
	 * Validate and deduplicate notification recipients.
	 *
	 * @param array<int, mixed> $recipients Raw recipients.
	 * @return array<int, string>
	 */
	private function sanitize_notification_recipients( array $recipients ): array {
		$valid_recipients = array();

		foreach ( $recipients as $recipient ) {
			if ( ! is_scalar( $recipient ) ) {
				continue;
			}

			$recipient = sanitize_email( (string) $recipient );

			if ( is_email( $recipient ) ) {
				$valid_recipients[] = $recipient;
			}
		}

		return array_values( array_unique( $valid_recipients ) );
	}

	/**
	 * Prepare admin notification body.
	 *
	 * @param int                  $submission_id Saved submission ID.
	 * @param array<string, mixed> $data Sanitized and validated payload.
	 * @return string
	 */
	private function prepare_admin_notification_message( int $submission_id, array $data ): string {
		$submitted_at = wp_date(
			get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
			current_time( 'timestamp' )
		);

		$lines = array(
			__( 'A new CAS FormFlow submission has been received.', 'cas-formflow' ),
			'',
			sprintf(
				/* translators: %d: submission ID. */
				__( 'Submission ID: %d', 'cas-formflow' ),
				$submission_id
			),
			sprintf(
				/* translators: %s: submission date and time. */
				__( 'Submitted at: %s', 'cas-formflow' ),
				$submitted_at
			),
			'',
			__( 'Contact details:', 'cas-formflow' ),
			sprintf(
				/* translators: %s: first name. */
				__( 'First name: %s', 'cas-formflow' ),
				$this->format_notification_value( $data['first_name'] )
			),
			sprintf(
				/* translators: %s: last name. */
				__( 'Last name: %s', 'cas-formflow' ),
				$this->format_notification_value( $data['last_name'] )
			),
			sprintf(
				/* translators: %s: email address. */
				__( 'Email: %s', 'cas-formflow' ),
				$this->format_notification_value( $data['email'] )
			),
			sprintf(
				/* translators: %s: phone number. */
				__( 'Phone: %s', 'cas-formflow' ),
				$this->format_notification_value( $data['phone'] )
			),
			sprintf(
				/* translators: %s: date of birth. */
				__( 'Date of birth: %s', 'cas-formflow' ),
				$this->format_notification_value( $data['date_of_birth'] )
			),
			'',
			__( 'Address:', 'cas-formflow' ),
			sprintf(
				/* translators: %s: country. */
				__( 'Country: %s', 'cas-formflow' ),
				$this->format_notification_value( $data['country'] )
			),
			sprintf(
				/* translators: %s: city. */
				__( 'City: %s', 'cas-formflow' ),
				$this->format_notification_value( $data['city'] )
			),
			sprintf(
				/* translators: %s: street address. */
				__( 'Street address: %s', 'cas-formflow' ),
				$this->format_notification_value( $data['street_address'] )
			),
			sprintf(
				/* translators: %s: postal code. */
				__( 'ZIP / Postal code: %s', 'cas-formflow' ),
				$this->format_notification_value( $data['postal_code'] )
			),
			'',
			__( 'Preferences:', 'cas-formflow' ),
			sprintf(
				/* translators: %s: terms acceptance status. */
				__( 'Terms accepted: %s', 'cas-formflow' ),
				$this->format_notification_bool( $data['terms'] )
			),
			sprintf(
				/* translators: %s: newsletter subscription status. */
				__( 'Newsletter: %s', 'cas-formflow' ),
				$this->format_notification_bool( $data['newsletter'] )
			),
		);

		return implode( "\n", $lines );
	}

	/**
	 * Format scalar notification field value.
	 *
	 * @param mixed $value Field value.
	 * @return string
	 */
	private function format_notification_value( mixed $value ): string {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return __( 'Not provided', 'cas-formflow' );
		}

		return $value;
	}

	/**
	 * Format checkbox-like notification value.
	 *
	 * @param mixed $value Checkbox value.
	 * @return string
	 */
	private function format_notification_bool( mixed $value ): string {
		return '1' === (string) $value
			? __( 'Yes', 'cas-formflow' )
			: __( 'No', 'cas-formflow' );
	}
}
