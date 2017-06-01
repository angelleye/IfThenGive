<?php

/**
 * This class defines all code necessary to Companies Setting from admin side
 * @class       AngellEYE_Give_When_PayPal_Connect_Setting
 * @version	1.0.0
 * @package     Givewhen
 * @subpackage  Givewhen/admin/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_PayPal_Connect_Setting {

    var $data = array();

    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */

    /**     * ***********************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     * ************************************************************************* */
    public function __construct() {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'ajax' => true        //does this table support ajax?
        ));
    }

    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {
        add_action('give_when_connect_to_paypal_create_setting', array(__CLASS__, 'give_when_connect_to_paypal_create_setting'));
        //add_action('give_when_connect_to_paypal_setting_save_field', array(__CLASS__, 'paypal_wp_button_manager_company_setting_save_field'));
        //add_action('give_when_connect_to_paypal_setting', array(__CLASS__, 'paypal_wp_button_manager_company_setting'));
        add_action( 'wp_ajax_request_permission', array(__CLASS__,'request_permission'));
        add_action("wp_ajax_nopriv_request_permission",  array(__CLASS__,'request_permission'));
        
    }

    public static function paypal_wp_button_manager_company_setting() {
        global $wpdb;

    }

    public static function give_when_connect_to_paypal_create_setting() {        
        
        $success_notice = get_option('give_when_permission_connect_to_paypal_success_notice');
        if($success_notice){
            echo '<div class="notice notice-success">';
                echo "<p>{$success_notice}</p>";                
            echo '</div>';
            delete_option('give_when_permission_connect_to_paypal_success_notice');
        }
        
        $failed_notice = get_option('give_when_permission_connect_to_paypal_failed_notice');
        if($failed_notice){
            echo '<div class="notice notice-error">';
                echo "<p>{$failed_notice}</p>";
            echo '</div>';
        }
        $conncet_to_paypal_flag = get_option('give_when_permission_connected_to_paypal');
        if($conncet_to_paypal_flag == 'Yes'){
        ?>
            <table class="form-table" id="give_when_callback_url">
                <tbody>
                    <tr valign="top">                        
                        <td>You are already connected to PayPal.</td>
                    </tr>
                    <tr valign="top">                        
                        <td>Your PayPal Details will display here.</td>
                    </tr>
                </tbody> 
            </table>
        <?php    
        }
        else {
        ?>
        <table class="form-table" id="give_when_callback_url">
            <tbody>
                <tr valign="top">                        
                    <td><button name="angelleye_connect_to_paypal" id="angelleye_connect_to_paypal" class="button button-primary">Connect To PayPal</button></td>
                </tr>
            </tbody> 
        </table>
        <div id="overlay" style=" background: #f6f6f6;opacity: 0.7;width: 100%;float: left;height: 100%;position: fixed;top: 0;z-index: 1031;text-align: center; display: none;">
            <div style="display: table; width:100%; height: 100%;">
                <div style="display: table-cell;vertical-align: middle;"><img src="<?php echo GW_PLUGIN_URL; ?>/admin/images/loading.gif"  style=" position: relative;top: 50%;"/>
                <h2>Please Don't Go back , We are redirecting you to PayPal.</h2></div>
            </div>            
        </div>
        <div class="notice notice-error" id="connect_paypal_error" style="display: none">
            <p id="connect_paypal_error_p"></p>
        </div>

<?php
        }   
    }

    /**
     * paypal_wp_button_manager_general_setting function used for display general setting block from admin side
     * @since    0.1.0
     * @access   public
     */
    public function request_permission() {
        
        $PayPal = new Adaptive(Give_When_PayPal_Helper::get_configuration());
        // Prepare request arrays        
        $Scope = array(
            'EXPRESS_CHECKOUT', 
            'DIRECT_PAYMENT', 
            'BILLING_AGREEMENT', 
            'REFERENCE_TRANSACTION', 
            'TRANSACTION_DETAILS',
            'TRANSACTION_SEARCH',
            'RECURRING_PAYMENTS',
            'ACCOUNT_BALANCE',
            'ENCRYPTED_WEBSITE_PAYMENTS',
            'REFUND',
            'NON_REFERENCED_CREDIT',
            'BUTTON_MANAGER',
            'MANAGE_PENDING_TRANSACTION_STATUS',
            'RECURRING_PAYMENT_REPORT',
            'EXTENDED_PRO_PROCESSING_REPORT',
            'EXCEPTION_PROCESSING_REPORT',
            'ACCOUNT_MANAGEMENT_PERMISSION',
            'ACCESS_BASIC_PERSONAL_DATA',
            'ACCESS_ADVANCED_PERSONAL_DATA'
        );

        $RequestPermissionsFields = array(
            'Scope' => $Scope,           
            'Callback' => site_url('?action=permission_callback')
        );        
        $PayPalRequestData = array('RequestPermissionsFields' => $RequestPermissionsFields);
        
        $PayPalResult = $PayPal->RequestPermissions($PayPalRequestData);
     
        if(empty($PayPalResult['Errors']) && $PayPalResult['Ack'] == 'Success'){
            echo json_encode(array( 'Ack' => $PayPalResult['Ack'] ,'Token' => $PayPalResult['Token'] , 'RedirectURL' => $PayPalResult['RedirectURL'] ));
        }
        else{
            echo json_encode(array('Ack' => $PayPalResult['Ack'] , 'Message' => $PayPalResult['Errors'][0]['Message'] , 'ErrorID' => $PayPalResult['Errors'][0]['ErrorID'] ));
        }
        exit;
    }
}

AngellEYE_Give_When_PayPal_Connect_Setting::init();
