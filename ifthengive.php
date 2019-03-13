<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.angelleye.com/
 * @since             0.1.0
 * @package           IfThenGive
 *
 * @wordpress-plugin
 * Plugin Name:       IfThenGive
 * Plugin URI:        http://www.angelleye.com/product/ifthengive-wordpress-donation-plugin/
 * Description:       Allow donors to create a billing agreement for automatic donations based on events / goals.  For example, If {event occurs} Then Give $x.xx.
 * Version:           0.2.0
 * Author:            Angell EYE
 * Author URI:        https://www.angelleye.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       ifthengive
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 *  define ITG_PLUGIN_URL constant for global use
 */
if (!defined('ITG_PLUGIN_DIR'))
    define('ITG_PLUGIN_DIR', dirname(__FILE__));

/**
 * define ITG_PLUGIN_URL constant for global use
 */
if (!defined('ITG_PLUGIN_URL'))
    define('ITG_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 *  define log file path
 */
if (!defined('ITG_LOG_DIR')) {
    define('ITG_LOG_DIR', ABSPATH . 'wp-content/uploads/ifthengive-logs/');
}

/**
 * define plugin basename
 */
if (!defined('ITG_PLUGIN_BASENAME')) {
    define('ITG_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

if (!defined('ITG_BUTTON_SOURCE')) {
    define('ITG_BUTTON_SOURCE', 'AngellEYE_GiveWhen');
}
/* This is passed to set_time_limit() at various points, to try to maximise run-time.  The effect of this varies according to the hosting setup - it can't necessarily always be controlled. */
if (!defined('ITG_PLUGIN_SET_TIME_LIMIT')) { 
    define('ITG_PLUGIN_SET_TIME_LIMIT', 0);
}

if (!defined('ITG_ISU_URL')) {
    define('ITG_ISU_URL', 'https://www.angelleye.com/web-services/ifthengive/paypal-isu/');
}

if (!defined('ITG_TEXT_DOMAIN')) {
    define('ITG_TEXT_DOMAIN', 'ifthengive');
}

if (!defined('AEU_ZIP_URL')) {
    define('AEU_ZIP_URL', 'http://downloads.angelleye.com/ae-updater/angelleye-updater/angelleye-updater.zip');
}

/**
 * Required functions
 */
if (!function_exists('angelleye_queue_update')) {
    require_once( 'includes/angelleye-functions.php' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ifthengive-activator.php
 */
function activate_ifthengive() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ifthengive-activator.php';
	IfThenGive_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ifthengive-deactivator.php
 */
function deactivate_ifthengive() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ifthengive-deactivator.php';
	IfThenGive_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ifthengive' );
register_deactivation_hook( __FILE__, 'deactivate_ifthengive' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ifthengive.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_ifthengive() {

	$plugin = new Ifthengive();
	$plugin->run();

}
run_ifthengive();
