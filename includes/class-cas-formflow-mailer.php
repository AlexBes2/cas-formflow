<?php
/**
 * SMTP mailer configuration.
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CAS_FormFlow_Mailer {

	/**
	 * Register mailer hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'phpmailer_init', array( $this, 'configure_smtp' ) );
		add_action( 'wp_mail_failed', array( $this, 'log_mail_failure' ) );
	}

	/**
	 * Configure WordPress PHPMailer instance for SMTP delivery.
	 *
	 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer WordPress PHPMailer instance.
	 * @return void
	 */
	public function configure_smtp( $phpmailer ): void {
		if ( ! $this->get_config_bool( 'CAS_FORMFLOW_SMTP_ENABLED', false ) ) {
			return;
		}

		$host = $this->get_config_string( 'CAS_FORMFLOW_SMTP_HOST' );

		if ( '' === $host ) {
			$this->debug_log( 'CAS FormFlow SMTP is enabled, but CAS_FORMFLOW_SMTP_HOST is empty.' );
			return;
		}

		$username   = $this->get_config_string( 'CAS_FORMFLOW_SMTP_USERNAME' );
		$password   = $this->get_config_string( 'CAS_FORMFLOW_SMTP_PASSWORD' );
		$encryption = strtolower( $this->get_config_string( 'CAS_FORMFLOW_SMTP_ENCRYPTION', 'tls' ) );
		$from_email = $this->get_config_string( 'CAS_FORMFLOW_SMTP_FROM_EMAIL' );
		$from_name  = $this->get_config_string(
			'CAS_FORMFLOW_SMTP_FROM_NAME',
			wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
		);

		$phpmailer->isSMTP();
		$phpmailer->Host       = $host;
		$phpmailer->Port       = $this->get_config_int( 'CAS_FORMFLOW_SMTP_PORT', 587 );
		$phpmailer->SMTPAuth   = $this->get_config_bool(
			'CAS_FORMFLOW_SMTP_AUTH',
			'' !== $username || '' !== $password
		);
		$phpmailer->Username   = $username;
		$phpmailer->Password   = $password;
		$phpmailer->SMTPAutoTLS = $this->get_config_bool( 'CAS_FORMFLOW_SMTP_AUTO_TLS', true );
		$phpmailer->SMTPDebug  = $this->get_config_int( 'CAS_FORMFLOW_SMTP_DEBUG', 0 );

		if ( in_array( $encryption, array( 'ssl', 'tls' ), true ) ) {
			$phpmailer->SMTPSecure = $encryption;
		} else {
			$phpmailer->SMTPSecure = '';
		}

		if ( is_email( $from_email ) ) {
			try {
				$phpmailer->setFrom( $from_email, $from_name, false );
			} catch ( Throwable $exception ) {
				$this->debug_log( 'CAS FormFlow SMTP from address error: ' . $exception->getMessage() );
			}
		}

		if ( $phpmailer->SMTPDebug > 0 ) {
			$phpmailer->Debugoutput = function ( string $message, int $level ): void {
				$this->debug_log(
					sprintf(
						'CAS FormFlow SMTP debug level %d: %s',
						$level,
						$message
					)
				);
			};
		}
	}

	/**
	 * Log wp_mail failures in debug mode.
	 *
	 * @param WP_Error $error Mail failure error.
	 * @return void
	 */
	public function log_mail_failure( WP_Error $error ): void {
		$this->debug_log( 'CAS FormFlow wp_mail failed: ' . $error->get_error_message() );
	}

	/**
	 * Get a config value from constants first, then environment variables.
	 *
	 * @param string $key Config key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	private function get_config_value( string $key, mixed $default = '' ): mixed {
		if ( defined( $key ) ) {
			return constant( $key );
		}

		$value = getenv( $key );

		if ( false !== $value ) {
			return $value;
		}

		return $default;
	}

	/**
	 * Get config as string.
	 *
	 * @param string $key Config key.
	 * @param string $default Default value.
	 * @return string
	 */
	private function get_config_string( string $key, string $default = '' ): string {
		return trim( (string) $this->get_config_value( $key, $default ) );
	}

	/**
	 * Get config as integer.
	 *
	 * @param string $key Config key.
	 * @param int    $default Default value.
	 * @return int
	 */
	private function get_config_int( string $key, int $default = 0 ): int {
		$value = $this->get_config_value( $key, $default );

		if ( is_numeric( $value ) ) {
			return (int) $value;
		}

		return $default;
	}

	/**
	 * Get config as boolean.
	 *
	 * @param string $key Config key.
	 * @param bool   $default Default value.
	 * @return bool
	 */
	private function get_config_bool( string $key, bool $default = false ): bool {
		$value = $this->get_config_value( $key, $default );

		if ( is_bool( $value ) ) {
			return $value;
		}

		return in_array( strtolower( (string) $value ), array( '1', 'true', 'yes', 'on' ), true );
	}

	/**
	 * Write debug logs only when WordPress debug mode is enabled.
	 *
	 * @param string $message Log message.
	 * @return void
	 */
	private function debug_log( string $message ): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}
}
