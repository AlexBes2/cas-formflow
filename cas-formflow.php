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

define( 'CAS_FORMFLOW_VERSION', '0.1.0' );
define( 'CAS_FORMFLOW_DB_VERSION', '1.0.0' );
define( 'CAS_FORMFLOW_FILE', __FILE__ );
define( 'CAS_FORMFLOW_PATH', plugin_dir_path( __FILE__ ) );
define( 'CAS_FORMFLOW_URL', plugin_dir_url( __FILE__ ) );

require_once CAS_FORMFLOW_PATH . 'includes/class-cas-formflow-activator.php';

register_activation_hook( CAS_FORMFLOW_FILE, array( 'CAS_FormFlow_Activator', 'activate' ) );