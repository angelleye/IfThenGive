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
        add_action('give_when_connect_to_paypal_create_setting', array(__CLASS__, 'paypal_wp_button_manager_company_create_setting'));
        //add_action('give_when_connect_to_paypal_setting_save_field', array(__CLASS__, 'paypal_wp_button_manager_company_setting_save_field'));
        //add_action('give_when_connect_to_paypal_setting', array(__CLASS__, 'paypal_wp_button_manager_company_setting'));
        add_action( 'wp_ajax_request_permission', array(__CLASS__,'request_permission'));
        add_action("wp_ajax_nopriv_request_permission",  array(__CLASS__,'request_permission'));
        
    }

    public static function paypal_wp_button_manager_company_setting() {
        global $wpdb;

    }

    public static function paypal_wp_button_manager_company_create_setting() {       
?>
        <table class="form-table" id="give_when_callback_url">
                <tbody>
<!--                    <tr valign="top">
                        <th scope="row"><label for="give_when_callback_url"><?php echo __('PayPal Permission Callback URL:', 'angelleye_give_when') ?></label></th>
                        <td>
                            <input type="text" class="large-text code" name="give_when_callback_url_input" value="<?php echo site_url('?AngellEYE_Give_When&action=permission_callback'); ?>" readonly>                           
                        </td>                        
                    </tr>-->
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

    /**
     * paypal_wp_button_manager_general_setting function used for display general setting block from admin side
     * @since    0.1.0
     * @access   public
     */
    public function request_permission() {
        
        $sandbox=TRUE;
        $developer_account_email = '';
        $application_id = 'APP-80W284485P519543T';
        $device_id = '';
        $api_username =  'tejasm-merchant_api2.itpathsolutions.co.in';
        $api_password =  'GJA2TBCF3U9H4VK9';
        $api_signature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31A47TBRQKcZyw6Bx9aDcmqr9ipPmt';
        $api_subject = '';
        $print_headers = '';
        $log_results = '';
        $log_path = '';
                
        // Create PayPal object.
        $PayPalConfig = array(
            'Sandbox' => $sandbox,
            'DeveloperAccountEmail' => $developer_account_email,
            'ApplicationID' => $application_id,
            'DeviceID' => $device_id,
            'IPAddress' => $_SERVER['REMOTE_ADDR'],
            'APIUsername' => $api_username,
            'APIPassword' => $api_password,
            'APISignature' => $api_signature,
            'APISubject' => $api_subject,
            'PrintHeaders' => $print_headers, 
            'LogResults' => $log_results, 
            'LogPath' => $log_path,
        );
        //$PayPal = new Angelleye_PayPal($PayPalConfig);
        $PayPal = new Adaptive($PayPalConfig);
        
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
           // 'Callback' => GW_PLUGIN_URL.'admin/partials/Permission_Callback.php'
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
