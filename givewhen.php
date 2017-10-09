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
 * @since             1.0.0
 * @package           GiveWhen
 *
 * @wordpress-plugin
 * Plugin Name:       GiveWhen
 * Plugin URI:        http://www.angelleye.com/product/give-when-wordpress-donation-plugin/
 * Description:       Allow donors to create a billing agreement to automatically Give a specified donation amount When a particular event or goal is achieved.
 * Version:           1.0.0
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
    define('ITG_LOG_DIR', ABSPATH . 'wp-content/uploads/give-when-logs/');
}

/**
 * define plugin basename
 */
if (!defined('ITG_PLUGIN_BASENAME')) {
    define('ITG_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

if (!defined('ITG_BUTTON_SOURCE')) {
    define('ITG_BUTTON_SOURCE', 'AngellEYE_IfThenGive');
}
/* This is passed to set_time_limit() at various points, to try to maximise run-time.  The effect of this varies according to the hosting setup - it can't necessarily always be controlled. */
if (!defined('ITG_PLUGIN_SET_TIME_LIMIT')) { 
    define('ITG_PLUGIN_SET_TIME_LIMIT', 0);
}

if (!defined('ITG_ISU_URL')) {
    define('ITG_ISU_URL', 'https://www.angelleye.com/web-services/givewhen/paypal-isu/');
}

if (!defined('ITG_TEXT_DOMAIN')) {
    define('ITG_TEXT_DOMAIN', 'ifthengive');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-givewhen-activator.php
 */
function activate_givewhen() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-givewhen-activator.php';
	Givewhen_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-givewhen-deactivator.php
 */
function deactivate_givewhen() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-givewhen-deactivator.php';
	Givewhen_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_givewhen' );
register_deactivation_hook( __FILE__, 'deactivate_givewhen' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-givewhen.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_givewhen() {

	$plugin = new Givewhen();
	$plugin->run();

}
run_givewhen();
