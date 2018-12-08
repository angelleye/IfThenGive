<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.angelleye.com/
 * @since      0.1.0
 *
 * @package    IfThenGive
 * @subpackage IfThenGive/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    IfThenGive
 * @subpackage IfThenGive/public
 * @author     Angell EYE <andrew@angelleye.com>
 */
class Ifthengive_Public {

    /**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    
    private $theme_template_path;
    private $plugin_template_path;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        $this->plugin_template_path = '/templates/';
        $this->theme_template_path = 'ifthengive/';
        
        $this->load_dependencies();        
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ifthengive_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ifthengive_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name . 'publicDataTablecss', plugin_dir_url(__FILE__).'css/datatables/jquery.dataTables.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'publicDataTable', plugin_dir_url(__FILE__).'css/datatables/dataTables.responsive.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'public_alertify_css',  ITG_PLUGIN_URL.'includes/css/alertify/alertify.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ifthengive-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ifthengive_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ifthengive_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name . 'DataTablejs', plugin_dir_url(__FILE__).'js/datatables/jquery.dataTables.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . 'DataTable', plugin_dir_url(__FILE__).'js/datatables/dataTables.responsive.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . 'public_alertify_js', ITG_PLUGIN_URL . 'includes/css/alertify/alertify.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ifthengive-public.js', array('jquery'), $this->version, false);        
        wp_localize_script($this->plugin_name, 'admin_ajax_url', admin_url('admin-ajax.php'));
    }

    public function load_dependencies() {
        /**
         * The class responsible for defining all actions that occur in the Frontend
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-ifthengive-public-display.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-ifthengive-list_my_transactions.php';        
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-ifthengive-list_my_goals.php';
        add_action('init', array($this,'ifthengive_init_shortcode'));        
    }

    public function ifthengive_init_shortcode(){
        add_shortcode( 'ifthengive_transactions', array($this,'itg_transactions_shortcode'));
        add_shortcode( 'ifthengive_account', array($this,'itg_account_shortcode'));
        add_shortcode( 'ifthengive_goals', array($this,'itg_goals_shortcode'));
        add_shortcode( 'ifthengive_account_info', array($this,'itg_account_info_shortcode'));
    }

    public static function ifthengive_locate_template($template_name, $theme_template_path = '', $plugin_template_path = '') {

        // Search template file in theme folder.        
        $template = locate_template(array(
            $theme_template_path . $template_name,
            $template_name
                ));
        
        $plugin_path = ITG_PLUGIN_DIR . $plugin_template_path;
        // Get plugins template file.
        if (!$template) :
            $template = $plugin_path . $template_name;
        endif;
        return apply_filters('ifthengive_locate_template', $template, $template_name, $theme_template_path, $plugin_template_path);
    }
    
    public static function itg_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {
        
        $template_name ='template-ifthengive-'.$template_name.'.php';
        
	if ( is_array( $args ) && isset( $args ) ) :
		extract( $args );
	endif;        
	$template_file = self::ifthengive_locate_template( $template_name, $tempate_path, $default_path );
	if ( ! file_exists( $template_file ) ) :
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '0.1.0' );
		return;
	endif;
	include $template_file;
    }
    
    /* rewrite function will be called at the time of Init action hook.
         * it will rewrite our thing with endpoint urls.
         * 
         */
        public function rewrite() {		
		add_rewrite_rule( '^itg-thankyou$', 'index.php?itgthankyou=1', 'top' );
                add_rewrite_rule( '^itg-error$', 'index.php?itgerror=1', 'top' );
                add_rewrite_rule( '^itg-account$', 'index.php?itgmyaccount=1', 'top' );
		if(get_transient( 'itg_flush' )) {
			delete_transient( 'itg_flush' );
			flush_rewrite_rules();
		}
	}  
        
        public function query_vars($vars){
            $vars[] = 'itgthankyou';
            $vars[] = 'itgerror';
            $vars[] = 'itgmyaccount';
            return $vars;
        }
        
        public function change_template($template) {       

        if (get_query_var('itgthankyou', false) !== false) {
            $template_file = self::ifthengive_locate_template( 'template-ifthengive-thankyou.php', $this->theme_template_path, $this->plugin_template_path );
            return $template_file;
        }
        
        if (get_query_var('itgerror', false) !== false) {
            $template_file = self::ifthengive_locate_template( 'template-ifthengive-errors.php', $this->theme_template_path, $this->plugin_template_path );
            return $template_file;
        }
        
        if (get_query_var('itgmyaccount', false) !== false) {
            $template_file = self::ifthengive_locate_template( 'template-ifthengive-my-account.php', $this->theme_template_path, $this->plugin_template_path );
            return $template_file;                        
        }
        
        //Fall back to original template
        return $template;
    }
    
    public function itg_transactions_shortcode() {
        if(!is_admin()){
            ob_start();
            self::itg_get_template('my-transactions',array(), $this->theme_template_path, $this->plugin_template_path);
            return ob_get_clean();
        }
    }
    
    public function itg_account_shortcode(){        
        if(!is_admin()){
            ob_start();
            self::itg_get_template('my-account',array(), $this->theme_template_path, $this->plugin_template_path);
            return ob_get_clean();
        }
    }
    
    public function itg_goals_shortcode(){
        if(!is_admin()){
            ob_start();
            self::itg_get_template('my-goals',array(), $this->theme_template_path, $this->plugin_template_path);
            return ob_get_clean();
        }
    }
    
    public function itg_account_info_shortcode(){
        if(!is_admin()){
            ob_start();
            self::itg_get_template('account-info',array(), $this->theme_template_path, $this->plugin_template_path);
            return ob_get_clean();
        }
    }

    public function itg_block_cgb_block_assets(){
        wp_enqueue_style(
            'itg_block-cgb-block-editor-css',
            ITG_PLUGIN_URL.'includes/gutenberg/blocks.style.build.css',
            array()
        );
    }
}
