<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.angelleye.com/
 * @since      0.1.0
 *
 * @package    IfThenGive
 * @subpackage IfThenGive/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.1.0
 * @package    IfThenGive
 * @subpackage IfThenGive/includes
 * @author     Angell EYE <andrew@angelleye.com>
 */
class Ifthengive_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ifthengive',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
