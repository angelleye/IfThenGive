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
                if($screen->post_type == 'give_when_goals' || $screen ->id == 'settings_page_give_when_option' || $screen ->id == 'dashboard_page_give_when_givers' ){
                   wp_enqueue_style($this->plugin_name . 'eight', GW_PLUGIN_URL.'includes/css/bootstrap/css/bootstrap.css', array(), $this->version, 'all');
                   wp_enqueue_style($this->plugin_name . 'nine',  GW_PLUGIN_URL.'includes/css/alertify/alertify.css', array(), $this->version, 'all');
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
                if($screen->post_type == 'give_when_goals' || $screen ->id == 'settings_page_give_when_option' || $screen ->id == 'dashboard_page_give_when_givers'){
                     wp_enqueue_script($this->plugin_name . 'six', GW_PLUGIN_URL . 'includes/css/bootstrap/js/bootstrap.min.js', array('jquery'), $this->version, false);
                     wp_enqueue_script($this->plugin_name . 'seven', GW_PLUGIN_URL . 'includes/css/clipboardjs/clipboard.min.js', array('jquery'), $this->version, false);
                     wp_enqueue_script($this->plugin_name . 'ten', GW_PLUGIN_URL . 'includes/css/alertify/alertify.min.js', array('jquery'), $this->version, false);
                }
                wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/givewhen-admin.js', array( 'jquery' ), $this->version, false );
                
                global $post;
                $args = array(
                    'post_type' => 'give_when_goals',
                    'post_status' => 'publish',
                    'posts_per_page' => '100'                    
                );

                $posts = get_posts($args);
		$give_when_posts = get_posts($args);                
		$shortcodes = array();
		$shortcodes_values = array();
                $shortcodes['[givewhen_my_transaction]']='GiveWhen Transaction';
                $shortcodes['[givewhen_my_account]']='GiveWhen Account';
                $shortcodes['[givewhen_my_goals]'] = 'My Signedup GiveWhen Goals';
                foreach ($give_when_posts as $key_post => $give_when_posts_value) {
			$shortcodes[$give_when_posts_value->ID] = $give_when_posts_value->post_title;
		}
		if (empty($shortcodes)) {

			$shortcodes_values = array('0' => 'No shortcode Available');
		} else {
			$shortcodes_values = $shortcodes;
		}
		wp_localize_script($this->plugin_name, 'gw_shortcodes_button_array', apply_filters('give_when_shortcode', array(
		'shortcodes_button' => $shortcodes_values
		)));
                
                $sanbox_enable = get_option('sandbox_enable_give_when', TRUE);                
                wp_localize_script($this->plugin_name, 'give_when_sanbox_enable_js', $sanbox_enable);
                
                wp_localize_script($this->plugin_name, 'admin_ajax_url', admin_url('admin-ajax.php'));
                
	}
        
    private function load_dependencies() {
        /*The class responsible for defining all actions that occur in the Dashboard for GiveWhen Goals. */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-give-when-post-types.php';
        
        /* The class responsible for defining give_when_billing_agrremment custom post types. */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-give-when-post-types-sign-up.php';
        
        /* The class responsible for defining give_when_billing_agrremment custom post types. */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-give-when-post-types-transactions.php';
        
        /* The class responsible for defining all actions that occur in the Dashboard */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/givewhen-admin-display.php';
        
        /* The class responsible for defining function for display Html element */
	require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-give-when-html-output.php';
        /* The class responsible for defining function for display company setting tab */
	require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-give-when-connect_paypal.php';
        //require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-give-when-general-settings.php';
        /*Custom class table */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/give-when-giver.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/give-when-list_transactions.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/give-when-list_users_transactions.php';
        /* The class responsible for cancel billing agreement of givers */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/give-when-cancel-billing-agreement.php';
    }
    
    /**
     *  give_when_shortcode_button_init function process for registering our button.
     *
     */
    public function give_when_shortcode_button_init() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
		return;

        //Add a callback to regiser our tinymce plugin
        add_filter('mce_external_plugins', array($this, 'give_when_register_tinymce_plugin'));

        // Add a callback to add our button to the TinyMCE toolbar
        add_filter('mce_buttons', array($this, 'give_when_add_tinymce_button'));
    }
    
    public function give_when_register_tinymce_plugin($plugin_array) {
        $plugin_array['give_when_shortcodes'] = plugin_dir_url(__FILE__) . 'js/givewhen-admin.js';
	return $plugin_array;                
    }

    public function give_when_add_tinymce_button($buttons) {
        array_push($buttons, 'give_when_shortcodes');
        return $buttons;
    }
      
    public function give_when_messages(){
        
        global $post, $post_ID;
        $post_ID = $post->ID;
        $post_type = get_post_type($post_ID);
        
        $custom_message = 'Goal Created Successfully';
        $messages['give_when_goals'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf(__('Goal Updated Successfully','givewhen')),
		2 => __('Custom field updated.','givewhen'),
		3 => __('Custom field deleted.','givewhen'),
		4 => __($custom_message,'givewhen'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf(__('Goal restored to revision from %s','givewhen'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
		6 => sprintf(__($custom_message,'givewhen')),
		7 => __('Goal saved.','givewhen'),
		8 => sprintf(__('Goal submitted. <a target="_blank" href="%s">Preview Goal</a>','givewhen'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
		9 => sprintf(__('Goal scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Goal</a>','givewhen'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
		10 => sprintf(__('Goal draft updated. <a target="_blank" href="%s">Preview Goal</a>','givewhen'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
		);
		return $messages;

        return $messages;
    }
    public function give_when_plugin_action_links( $links, $file ){
           $plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . 'givewhen.php' );
           if($file == $plugin_basename)
           {
               $new_links = array(
                   sprintf( '<a href="%s">%s</a>', admin_url('options-general.php?page=give_when_option'), __( 'Configure', 'givewhen' ) ),
                   sprintf( '<a href="%s" target="_blank">%s</a>', 'https://www.angelleye.com/category/docs/givewhen-wordpress', __( 'Docs', 'givewhen' ) ),
                   sprintf( '<a href="%s" target="_blank">%s</a>', '#', __( 'Support', 'givewhen' ) ),
                   sprintf( '<a href="%s" target="_blank">%s</a>', '#', __( 'Write a Review', 'givewhen' ) ),
               );

               $links = array_merge( $links, $new_links );
           }
           return $links;
    }
}
