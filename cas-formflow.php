<?php
/**
 * Plugin Name:       CAS FormFlow
 * Description:       Multi-step contact form plugin.
 * Version:           0.1.1
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

define( 'CAS_FORMFLOW_VERSION', '0.1.1' );
define( 'CAS_FORMFLOW_DB_VERSION', '0.1.0' );
define( 'CAS_FORMFLOW_MIN_AGE', 18 );
define( 'CAS_FORMFLOW_MIN_DATE_OF_BIRTH', '1900-01-01' );
define( 'CAS_FORMFLOW_PLUGIN_FILE', __FILE__ );
define( 'CAS_FORMFLOW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAS_FORMFLOW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once CAS_FORMFLOW_PLUGIN_DIR . 'includes/class-cas-formflow-activator.php';
require_once CAS_FORMFLOW_PLUGIN_DIR . 'includes/class-cas-formflow-plugin.php';

register_activation_hook( CAS_FORMFLOW_PLUGIN_FILE, array( 'CAS_FormFlow_Activator', 'activate' ) );

function cas_formflow_run_plugin(): void {
	$plugin = new CAS_FormFlow_Plugin();
	$plugin->run();
}

cas_formflow_run_plugin();
