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
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/autoload.php';

                /**
                 * PayPal php class file included.
                 */
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/Angelleye_PayPal.php';
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/Adaptive.php';
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/PPAuth.php';
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/AuthUtil.php';                     
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/give-when-paypal-helper.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-givewhen-public.php';

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
            global $wp;            
            if (isset($_GET['action']) && $_GET['action'] == 'permission_callback') {
                 if(!empty($_GET['request_token']) && !empty($_GET['verification_code'])){
                     $paypal_helper_object = new Give_When_PayPal_Helper();                     
                     $PayPal = new Adaptive($paypal_helper_object->get_configuration());
                    
                    $GetAccessTokenFields = array(
                      'Token' => $_REQUEST['request_token'], 	
                      'Verifier' => $_REQUEST['verification_code']
                    );                    
                    $PayPalRequestData = array('GetAccessTokenFields' => $GetAccessTokenFields);
                    
                    $PayPalResult = $PayPal->GetAccessToken($PayPalRequestData);                    
                    if($PayPalResult['Ack'] == 'Success'){                        
                        $paypal_helper_object->set_tokens($PayPalResult['Token'], $PayPalResult['TokenSecret']);                                                
                        $paypal_for_peronal_data = new Adaptive($paypal_helper_object->get_third_party_configuration());
                        // Prepare request arrays
                        $AttributeList = array(
                            'http://axschema.org/namePerson/first',
                            'http://axschema.org/namePerson/last',
                            'http://axschema.org/contact/email',
                            'http://axschema.org/contact/country/home',
                            'https://www.paypal.com/webapps/auth/schema/payerID'
                        );
                        $PayPalResultPeronalData = $paypal_for_peronal_data->GetBasicPersonalData($AttributeList);                        
                        if($PayPalResultPeronalData['Ack']='Success'){
                            foreach($PayPalResultPeronalData['PersonalData'] as $PayPalPerson){
                                $key = substr($PayPalPerson['PersonalDataKey'], strrpos($PayPalPerson['PersonalDataKey'], '/') + 1);
                                update_option('give_when_permission_connected_person_'.$key,$PayPalPerson['PersonalDataValue']);
                            }
                        }                        
                        update_option( 'give_when_permission_connected_to_paypal', 'Yes');
                        update_option( 'give_when_permission_token', $PayPalResult['Token'] );
                        update_option( 'give_when_permission_token_secret', $PayPalResult['TokenSecret'] );
                        update_option( 'give_when_permission_connect_to_paypal_success_notice', 'You are successfully connected with PayPal.');
                    }
                    else{
                        update_option( 'give_when_permission_connect_to_paypal_failed_notice', $PayPalResult['Ack'].' : Something went wrong. Please try again.');
                    }                    
                    wp_redirect(admin_url('admin.php?page=give_when_option&tab=connect_to_paypal'));
                    die();
                 }
            }
            
            if (isset($_GET['action']) && $_GET['action'] == 'ec_return') {
                $token = $_GET['token'];                
                $PayPal_config = new Give_When_PayPal_Helper();   
                $paypal_account_id = get_option('give_when_permission_connected_person_payerID');        
                $PayPal_config->set_api_subject($paypal_account_id);                
                $PayPal = new Angelleye_PayPal($PayPal_config->get_configuration());
               
                $PayPalResultGEC = $PayPal->GetExpressCheckoutDetails($token);
                $post_id='';
                if($PayPal->APICallSuccessful($PayPalResultGEC['ACK'])){
                    $temp = $PayPalResultGEC['CUSTOM'];
                    $arr = explode('|',$temp);
                    $amount_array = explode('_',$arr[0]);
                    $amount = $amount_array[1];
                    $post_array = explode('_',$arr[1]);
                    $post_id = $post_array[2];                    
                    update_post_meta($post_id,'give_when_gec_email_'.$PayPalResultGEC['PAYERID'],$PayPalResultGEC['EMAIL']);
                    update_post_meta($post_id,'give_when_gec_payer_id_'.$PayPalResultGEC['PAYERID'],$PayPalResultGEC['PAYERID']);
                    update_post_meta($post_id,'give_when_gec_first_name_'.$PayPalResultGEC['PAYERID'],$PayPalResultGEC['FIRSTNAME']);
                    update_post_meta($post_id,'give_when_gec_last_name_'.$PayPalResultGEC['PAYERID'],$PayPalResultGEC['LASTNAME']);
                    update_post_meta($post_id,'give_when_gec_country_code_'.$PayPalResultGEC['PAYERID'],$PayPalResultGEC['COUNTRYCODE']);
                    update_post_meta($post_id,'give_when_gec_currency_code_'.$PayPalResultGEC['PAYERID'],$PayPalResultGEC['CURRENCYCODE']);
                    update_post_meta($post_id,'give_when_gec_amount_'.$PayPalResultGEC['PAYERID'],$amount);
                    
                    $role = get_role( 'giver' );
                    if($role==NULL){
                        add_role('giver','Giver');
                    }
                    $userdata=array(
                        'user_pass' => 'password',
                        'user_login' => $PayPalResultGEC['EMAIL'],
                        'user_email' => $PayPalResultGEC['EMAIL'],
                        'display_name' => $PayPalResultGEC['FIRSTNAME'].' '.$PayPalResultGEC['LASTNAME'],
                        'first_name' => $PayPalResultGEC['FIRSTNAME'],
                        'last_name' => $PayPalResultGEC['LASTNAME'],
                        'role' => 'giver'
                    );
                    $user_exist = email_exists($PayPalResultGEC['EMAIL']);
                    if($user_exist){
                        $userdata['ID'] = $user_exist;
                    }                    
                    $user_id = wp_insert_user($userdata);
                    if( is_wp_error( $user_id ) ) {
                        $error = 'Error on user creation: ' . $user_id->get_error_message();
                        print_r($err);
                        exit;
                    }
                    else{
                        update_post_meta($post_id,'give_when_gec_wp_user_id_'.$PayPalResultGEC['PAYERID'],$user_id);
                    }
                }
                else{
                    echo "<pre>";
                    var_dump($PayPalResultGEC['ERRORS']);
                    exit;
                }
                
                $PayPalResultCBA = $PayPal->CreateBillingAgreement($token);
                if($PayPal->APICallSuccessful($PayPalResultCBA['ACK'])){
                    update_post_meta($post_id,'give_when_gec_billing_agreement_id_'.$PayPalResultGEC['PAYERID'],$PayPalResultCBA['BILLINGAGREEMENTID']);
                }
                else{
                    echo "<pre>";
                    var_dump($PayPalResultCBA['ERRORS']);
                    exit;
                }
            }
        }
}
