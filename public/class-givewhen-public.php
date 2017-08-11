<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 *
 * @package    Givewhen
 * @subpackage Givewhen/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Givewhen
 * @subpackage Givewhen/public
 * @author     Angell EYE <andrew@angelleye.com>
 */
class Givewhen_Public {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->load_dependencies();        
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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
        wp_enqueue_style($this->plugin_name . 'publicDataTablecss', '//cdn.datatables.net/1.10.7/css/jquery.dataTables.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'publicDataTable', '//cdn.datatables.net/responsive/1.0.6/css/dataTables.responsive.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/givewhen-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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
        wp_enqueue_script($this->plugin_name . 'DataTablejs', '//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . 'DataTable', '//cdn.datatables.net/responsive/1.0.6/js/dataTables.responsive.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/givewhen-public.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'admin_ajax_url', admin_url('admin-ajax.php'));
    }

    public function load_dependencies() {
        /**
         * The class responsible for defining all actions that occur in the Frontend
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/givewhen-public-display.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/give-when-list_my_transactions.php';        
        add_shortcode( 'givewhen_my_transaction', array(__CLASS__,'givewhen_my_transaction_shortcode'));
    }

    public static function give_when_locate_template($template_name, $template_path = '', $default_path = '') {
        // Set variable to search in the templates folder of theme.
        if (!$template_path) :
            $template_path = 'templates/';
        endif;
        // Set default plugin templates path.
        if (!$default_path) :
            $default_path = GW_PLUGIN_DIR . '/templates/'; // Path to the template folder
        endif;
        // Search template file in theme folder.
        $template = locate_template(array(
            $template_path . $template_name,
            $template_name
                ));
        // Get plugins template file.
        if (!$template) :
            $template = $default_path . $template_name;
        endif;
        return apply_filters('give_when_locate_template', $template, $template_name, $template_path, $default_path);
    }
    
    public static function gw_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {
	if ( is_array( $args ) && isset( $args ) ) :
		extract( $args );
	endif;
	$template_file = self::give_when_locate_template( $template_name, $tempate_path, $default_path );
	if ( ! file_exists( $template_file ) ) :
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
		return;
	endif;
	include $template_file;
    }
    
    /* rewrite function will be called at the time of Init action hook.
         * it will rewrite our thing with endpoint urls.
         * 
         */
        public function rewrite() {		
		add_rewrite_rule( '^give-when-thankyou$', 'index.php?gwthankyou=1', 'top' );
                add_rewrite_rule( '^give-when-error$', 'index.php?gwerror=1', 'top' );
		if(get_transient( 'gw_flush' )) {
			delete_transient( 'gw_flush' );
			flush_rewrite_rules();
		}
	}  
        
        public function query_vars($vars){
            $vars[] = 'gwthankyou';
            $vars[] = 'gwerror';
            return $vars;
        }
        
        public function change_template($template) {       

        if (get_query_var('gwthankyou', false) !== false) {            

            $newTemplate = locate_template(array('goal-signup-complete.php'));            
            if ('' != $newTemplate)
                return $newTemplate;

            //Check plugin directory next
            $newTemplate = GW_PLUGIN_DIR . '/templates/goal-signup-complete.php';
            if (file_exists($newTemplate))
                return $newTemplate;
        }
        
        if (get_query_var('gwerror', false) !== false) {            

            $newTemplate = locate_template(array('gw-errors-display.php'));            
            if ('' != $newTemplate)
                return $newTemplate;

            //Check plugin directory next
            $newTemplate = GW_PLUGIN_DIR . '/templates/gw-errors-display.php';
            if (file_exists($newTemplate))
                return $newTemplate;
        }
                
        //Fall back to original template
        return $template;
    }
    
    public static function givewhen_my_transaction_shortcode() {
        $template = self::gw_get_template('givewhen-my-transactions.php');
        return $template; 
    }

}
