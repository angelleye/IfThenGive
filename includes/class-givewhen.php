<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 *
 * @package    Givewhen
 * @subpackage Givewhen/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Givewhen
 * @subpackage Givewhen/includes
 * @author     Angell EYE <andrew@angelleye.com>
 */
class Givewhen {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Givewhen_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'givewhen';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
                //Handle callback response.
                add_action('parse_request', array($this, 'handle_callback_permission'), 0);               
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Givewhen_Loader. Orchestrates the hooks of the plugin.
	 * - Givewhen_i18n. Defines internationalization functionality.
	 * - Givewhen_Admin. Defines all hooks for the admin area.
	 * - Givewhen_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-givewhen-loader.php';

                /**
                * The class responsible for writing log in log file.
                * core plugin.
                */
                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-give-when-logger.php';
                
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-givewhen-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-givewhen-admin.php';
                
                /**
                * Givewhen create/edit interface code written
                */
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-give-when-html-format.php';
                
                /**
                * Autoload file included for paypal intigrate paypal library.
                */
                require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/autoload.php';
                /**
                 * PayPal php class file included.
                 */                
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/give-when-paypal-helper.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-givewhen-public.php';
                /**
                 * Included for inherit wordpress table style.
                **/
                
                /**
                * PayPal Debug Log
                */
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-give-when-log.php';

                if ( !class_exists( 'WP_List_Table' ) ){
                require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
                }
                
                
		$this->loader = new Givewhen_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Givewhen_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Givewhen_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Givewhen_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
                $this->loader->add_action('admin_init', $plugin_admin, 'give_when_shortcode_button_init');
                $this->loader->add_filter('post_updated_messages', $plugin_admin, 'give_when_messages');
                $this->loader->add_filter('plugin_row_meta', $plugin_admin, 'give_when_plugin_action_links', 10, 2);
    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Givewhen_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
                
                $this->loader->add_action( 'init', $plugin_public, 'rewrite');
                $this->loader->add_filter( 'query_vars', $plugin_public, 'query_vars');
                $this->loader->add_action( 'template_include', $plugin_public, 'change_template');                
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Givewhen_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
        
        /**
        * API request - Trigger any API requests
        *
        * @access public
        * @since 1.0.0
        * @return void
        */
        public function handle_callback_permission() {
            @session_start();
            global $wp;
            if (isset($_GET['action']) && $_GET['action'] == 'permission_callback') {
                $sanbox_enable = get_option('sandbox_enable_give_when');
                if($sanbox_enable === 'yes'){
                    $sandbox = 'true';
                }else{
                    $sandbox = 'false';
                }
                if(isset($_GET['merchantIdInPayPal'])){
                    $url = 'https://www.angelleye.com/web-services/givewhen/paypal-isu/';
                    $postData = "sandbox={$sandbox}&api=account_detail&merchantIdInPayPal={$_GET['merchantIdInPayPal']}";
                    $AccountDetail = AngellEYE_Give_When_PayPal_Connect_Setting::curl_request($url,$postData);
                    $AccountDetailArray = json_decode($AccountDetail,true);
                                        
                    if($sandbox=='true'){
                        update_option('give_when_permission_sandbox_connected_person_merchant_id',$AccountDetailArray['DATA']['merchant_id']);
                        update_option('give_when_sandbox_api_credentials_api_user_name',$AccountDetailArray['DATA']['api_credentials']['signature']['api_user_name']);
                        update_option('give_when_sandbox_api_credentials_api_password',$AccountDetailArray['DATA']['api_credentials']['signature']['api_password']);
                        update_option('give_when_sandbox_api_credentials_signature',$AccountDetailArray['DATA']['api_credentials']['signature']['signature']);
                        update_option('give_when_sandbox_connected_to_paypal', 'Yes');
                    }
                    else{
                        update_option('give_when_permission_live_connected_person_merchant_id',$AccountDetailArray['DATA']['merchant_id']);
                        update_option('give_when_live_api_credentials_api_user_name',$AccountDetailArray['DATA']['api_credentials']['signature']['api_user_name']);
                        update_option('give_when_live_api_credentials_api_password',$AccountDetailArray['DATA']['api_credentials']['signature']['api_password']);
                        update_option('give_when_live_api_credentials_signature',$AccountDetailArray['DATA']['api_credentials']['signature']['signature']);
                        update_option('give_when_live_connected_to_paypal', 'Yes');
                    }                    
                    update_option( 'give_when_permission_connect_to_paypal_success_notice', 'You are successfully connected with PayPal.');
                }
                else{
                    update_option( 'give_when_permission_connect_to_paypal_failed_notice', 'Callback from PayPal : Something went wrong. Please try again.');                       
                }
                wp_redirect(admin_url('admin.php?page=give_when_option&tab=connect_to_paypal'));
                die();
            }
            if (isset($_GET['action']) && $_GET['action'] == 'ec_return') {
                if(!session_id()) {
                    session_start();
                }                
                $token = $_GET['token'];                
                $PayPal_config = new Give_When_PayPal_Helper();                
                $PayPal_config->set_api_cedentials();                
                $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
               
                $PayPalResultGEC = $PayPal->GetExpressCheckoutDetails($token);                
                if($PayPal->APICallSuccessful($PayPalResultGEC['ACK'])){
                    
                    $temp = $PayPalResultGEC['CUSTOM'];
                    $arr = explode('|',$temp);
                    $amount_array = explode('_',$arr[0]);
                    $amount = $amount_array[1];
                    
                    $post_array = explode('_',$arr[1]);
                    $goal_post_id = $post_array[2];
                    
                    $user_array = explode('_',$arr[2]);                   
                    $goal_user_id = $user_array[2];
                }
                else{
                    $_SESSION['GW_Error'] = true;
                    $_SESSION['GW_Error_Type'] = __('PayPal Error','givewhen');
                    $_SESSION['GW_Error_Array'] = $PayPalResultGEC['ERRORS'];                    
                    /* save log */
                    $debug = (get_option('log_enable_give_when') == 'yes') ? 'yes' : 'no';
                    if ('yes' == $debug) {
                        $logArray = '';
                        $logArray = $PayPalResultGEC;
                        $logArray['RAWREQUEST'] = $PayPal->MaskAPIResult($PayPalResultGEC['RAWREQUEST']);
                        $logArray['REQUESTDATA'] = $PayPal->NVPToArray($logArray['RAWREQUEST']);
                        $log_write = new AngellEYE_Give_When_Logger();
                        $log_write->add('angelleye_give_when_express_checkout', 'GetExpressCheckout Failed : ' . print_r($logArray, true), 'express_checkout');
                    }
                    wp_redirect(site_url('give-when-error'));
                    exit;
                }
                    $PayPalResultCBA = $PayPal->CreateBillingAgreement($token);
                    if($PayPal->APICallSuccessful($PayPalResultCBA['ACK'])){
                        
                        /*inserting new user and if user_id is available then update user.*/
                        $goal_user_id = wp_insert_user($_SESSION['gw_user_data']);
                        if( is_wp_error( $goal_user_id ) ) {
                            $error = __('Error on user creation: ','givewhen') . $user_id->get_error_message();
                            echo json_encode(array('Ack'=>__('Failure','givewhen'),'ErrorCode'=>__('WP Error','givewhen'),'ErrorShort'=>__('Error on user creation:','givewhen'),'ErrorLong'=>$error));
                            exit;
                        }
                        else{                
                            /*it makes user a normal login*/
                            if($_SESSION['gw_guest_user'] == 'no'){
                                wp_new_user_notification($goal_user_id,null,'user');
                                wp_set_auth_cookie( $goal_user_id, true );
                            }                            
                        }
                            
                        /* save GetExpressCheckoutDetails to User Meta */
                        update_user_meta($goal_user_id, 'give_when_gec_email', $PayPalResultGEC['EMAIL']);                     
                        update_user_meta($goal_user_id,'give_when_gec_payer_id',$PayPalResultGEC['PAYERID']);
                        update_user_meta($goal_user_id,'give_when_gec_first_name',$PayPalResultGEC['FIRSTNAME']);
                        update_user_meta($goal_user_id,'give_when_gec_last_name',$PayPalResultGEC['LASTNAME']);
                        update_user_meta($goal_user_id,'give_when_gec_country_code',$PayPalResultGEC['COUNTRYCODE']);
                        update_user_meta($goal_user_id,'give_when_gec_currency_code',$PayPalResultGEC['CURRENCYCODE']);
                        update_user_meta($goal_user_id,'give_when_guest_user',$_SESSION['gw_guest_user']);
                        $signedup_goals= get_user_meta($goal_user_id,'give_when_signedup_goals',true);
                        if($signedup_goals !=''){
                        $signedup_goals = $signedup_goals."|".$goal_post_id;
                        }
                        else{
                            $signedup_goals = $goal_post_id;
                        }                    
                        update_user_meta($goal_user_id,'give_when_signedup_goals',$signedup_goals);
                        
                        /*unset session variable*/
                        unset($_SESSION['gw_user_data']);
                        unset($_SESSION['gw_guest_user']);
                        /* Save BILLING AGREEMENT ID in the UserMeta */
                        update_user_meta($goal_user_id,'give_when_gec_billing_agreement_id',$PayPalResultCBA['BILLINGAGREEMENTID']);

                        /* Create new post for signup post type and save goal_id,user_id,amount */
                        $new_post_id = wp_insert_post( array(
                            'post_author' => $goal_user_id,
                            'post_status' => 'publish',
                            'post_type' => 'give_when_sign_up',
                            'post_title' => ('User ID : '.$goal_user_id.'& Goal ID : '.$goal_post_id)
                        ) );

                        update_post_meta($new_post_id,'give_when_signup_amount',$amount);                    
                        update_post_meta($new_post_id,'give_when_signup_wp_user_id',$goal_user_id);
                        update_post_meta($new_post_id,'give_when_signup_wp_goal_id',$goal_post_id);    
                        /*save log*/
                        $debug = (get_option('log_enable_give_when') == 'yes') ? 'yes' : 'no';
                        if ('yes' == $debug) {
                                $logArray = '';
                                $logArray = $PayPalResultCBA;
                                $logArray['RAWREQUEST'] = $PayPal->MaskAPIResult($PayPalResultCBA['RAWREQUEST']);
                                $logArray['REQUESTDATA'] = $PayPal->NVPToArray($logArray['RAWREQUEST']);
                                $log_write = new AngellEYE_Give_When_Logger();
                                $log_write->add('angelleye_give_when_express_checkout', 'CreateBillingAgreement Success : ' . print_r($logArray, true), 'express_checkout');
                        }
                        $amount = base64_encode($amount);
                        $urlusr = base64_encode($goal_user_id);
                        $post = get_post($goal_post_id); 
                        $slug = $post->post_name;
                        wp_redirect(site_url('give-when-thankyou?goal='.$slug.'&amt='.$amount.'&user='.$urlusr));
                        exit;
                    }
                    else{
                        $_SESSION['GW_Error'] = true;
                        $_SESSION['GW_Error_Type'] = 'PayPalError';
                        $_SESSION['GW_Error_Array'] = $PayPalResultCBA;
                        /*save log*/
                        $debug = (get_option('log_enable_give_when') == 'yes') ? 'yes' : 'no';
                        if ('yes' == $debug) {
                                $logArray = '';
                                $logArray = $PayPalResultCBA;
                                $logArray['RAWREQUEST'] = $PayPal->MaskAPIResult($PayPalResultCBA['RAWREQUEST']);
                                $logArray['REQUESTDATA'] = $PayPal->NVPToArray($logArray['RAWREQUEST']);
                                $log_write = new AngellEYE_Give_When_Logger();
                                $log_write->add('angelleye_give_when_express_checkout', 'CreateBillingAgreement Failed : ' . print_r($logArray, true), 'express_checkout');
                        }
                        wp_redirect(site_url('give-when-error'));
                        exit;
                    }
            }                        
        }                
}
