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
     * Returns singleton instance.
     *
     * @return CAS_FormFlow
     */
    public static function get_instance() {
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
     * Registers base hooks.
     *
     * @return void
     */
    private function register_hooks() {
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
    }

    /**
     * Loads translations.
     *
     * @return void
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'cas-formflow',
            false,
            dirname( CAS_FORMFLOW_BASENAME ) . '/languages/'
        );
    }
}
