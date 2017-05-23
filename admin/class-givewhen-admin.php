<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 *
 * @package    Givewhen
 * @subpackage Givewhen/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Givewhen
 * @subpackage Givewhen/admin
 * @author     Angell EYE <andrew@angelleye.com>
 */
class Givewhen_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->load_dependencies();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Givewhen_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Givewhen_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */                 
                $screen = get_current_screen();
                if($screen->post_type == 'give_when'){
                   wp_enqueue_style($this->plugin_name . 'eight', GW_PLUGIN_URL.'includes/css/bootstrap/css/bootstrap.css', array(), $this->version, 'all');
                }
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/givewhen-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Givewhen_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Givewhen_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
                $screen = get_current_screen();
                if($screen->post_type == 'give_when'){
                     wp_enqueue_script($this->plugin_name . 'six', GW_PLUGIN_URL . 'includes/css/bootstrap/js/bootstrap.min.js', array('jquery'), $this->version, false);
                     wp_enqueue_script($this->plugin_name . 'seven', GW_PLUGIN_URL . 'includes/css/clipboardjs/clipboard.min.js', array('jquery'), $this->version, false);
                }
                wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/givewhen-admin.js', array( 'jquery' ), $this->version, false );
		$shortcodes = array();
		$shortcodes_values = array();
		if (empty($shortcodes)) {

			$shortcodes_values = array('0' => 'No shortcode Available');
		} else {
			$shortcodes_values = $shortcodes;
		}
		wp_localize_script('paypal-wp-button-manager', 'shortcodes_button_array', apply_filters('paypal_wp_button_manager_shortcode', array(
		'shortcodes_button' => $shortcodes_values
		)));

	}
        
    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-give-when-post-types.php';
    }

}
