<?php
/**
 * Plugin Name:       CAS FormFlow
 * Description:       Multi-step contact form plugin.
 * Version:           0.1.0
 * Requires at least: 6.9
 * Requires PHP:      8.3
 * Author:            AlexBes
 * Text Domain:       cas-formflow
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'CAS_FORMFLOW_VERSION' ) ) {
    define( 'CAS_FORMFLOW_VERSION', '0.1.0' );
}

if ( ! defined( 'CAS_FORMFLOW_FILE' ) ) {
    define( 'CAS_FORMFLOW_FILE', __FILE__ );
}

if ( ! defined( 'CAS_FORMFLOW_BASENAME' ) ) {
    define( 'CAS_FORMFLOW_BASENAME', plugin_basename( CAS_FORMFLOW_FILE ) );
}

if ( ! defined( 'CAS_FORMFLOW_PATH' ) ) {
    define( 'CAS_FORMFLOW_PATH', plugin_dir_path( CAS_FORMFLOW_FILE ) );
}

if ( ! defined( 'CAS_FORMFLOW_URL' ) ) {
    define( 'CAS_FORMFLOW_URL', plugin_dir_url( CAS_FORMFLOW_FILE ) );
}

if ( ! defined( 'CAS_FORMFLOW_INC_PATH' ) ) {
    define( 'CAS_FORMFLOW_INC_PATH', CAS_FORMFLOW_PATH . 'includes/' );
}

if ( ! defined( 'CAS_FORMFLOW_ASSETS_URL' ) ) {
    define( 'CAS_FORMFLOW_ASSETS_URL', CAS_FORMFLOW_URL . 'assets/' );
}

require_once CAS_FORMFLOW_INC_PATH . 'class-cas-formflow.php';

/**
 * Boots the plugin.
 *
 * @return CAS_FormFlow
 */
function cas_formflow() {
    return CAS_FormFlow::get_instance();
}

add_action( 'plugins_loaded', 'cas_formflow' );
