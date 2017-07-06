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
        add_action('give_when_connect_to_paypal_setting_save_field', array(__CLASS__, 'give_when_connect_to_paypal_setting_save_field'));
        //add_action('give_when_connect_to_paypal_setting', array(__CLASS__, 'paypal_wp_button_manager_company_setting'));
        add_action( 'wp_ajax_request_permission', array(__CLASS__,'request_permission'));
        add_action("wp_ajax_nopriv_request_permission",  array(__CLASS__,'request_permission'));
        
    }

    public static function give_when_connect_to_paypal_setting_fields() {
        global $wpdb;
        $Logger = new AngellEYE_Give_When_Logger();
        $fields[] = array(
            'title' => __('Sandbox', 'angelleye_give_when'),
            'id' => 'sandbox_enable_give_when',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'angelleye_give_when'),
            'default' => 'no',
            'labelClass' => 'switch',
            'desc' => sprintf(__('<div class="slider round"></div>', 'angelleye_give_when'))
        );
        $fields[] = array(
            'title' => __('Debug Log', 'angelleye_give_when'),
            'id' => 'log_enable_give_when',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'angelleye_give_when'),
            'default' => 'no',
            'desc' => sprintf(__('Log Give When plugin events in <code>%s</code>', 'angelleye_give_when'), $Logger->give_when_for_wordpress_wordpress_get_log_file_path('angelleye_give_when'))
        );        
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options_setting');
        return $fields;
    }

    public static function give_when_connect_to_paypal_create_setting() {        
        
        $success_notice = get_option('give_when_permission_connect_to_paypal_success_notice');
        if($success_notice){            
            echo '<div class="alert alert-success">';
                echo "<strong>{$success_notice}</strong>";
            echo '</div>';
            delete_option('give_when_permission_connect_to_paypal_success_notice');
        }
        
        $failed_notice = get_option('give_when_permission_connect_to_paypal_failed_notice');
        if($failed_notice){              
            echo '<div class="alert alert-danger">';
                echo "<strong>{$failed_notice}</strong>";
            echo '</div>';
            delete_option('give_when_permission_connect_to_paypal_failed_notice');
        }
        $conncet_to_paypal_flag = get_option('give_when_permission_connected_to_paypal');
        $genral_setting_fields = self::give_when_connect_to_paypal_setting_fields();
        $Html_output = new AngellEYE_Give_When_Html_output();
        if($conncet_to_paypal_flag == 'Yes'){
        ?>  
            <div class="wrap">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h1 class="text-info">&nbsp;PayPal User Infromation</h1>
                        </div>                        
                        <div class="clearfix"></div>
                    </div>                    
                    <br>
                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-sm-6 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-responsive">
                                    <tbody>
                                        <tr>        
                                            <td>
                                                <strong>
                                                    <span class="glyphicon glyphicon-asterisk text-primary"></span>
                                                    PayPal Account ID                                                
                                                </strong>
                                            </td>
                                            <td class="text-primary">
                                                <?php 
                                                    $paypal_account_id = get_option('give_when_permission_connected_person_payerID');
                                                    echo isset($paypal_account_id) ? $paypal_account_id :'';
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>    
                                            <td>
                                                <strong>
                                                    <span class="glyphicon glyphicon-user  text-primary"></span>    
                                                    Name                                                
                                                </strong>
                                            </td>
                                            <td class="text-primary">
                                                <?php
                                                    $paypal_first_name = get_option('give_when_permission_connected_person_first');
                                                    $paypal_last_name = get_option('give_when_permission_connected_person_last');
                                                    echo $paypal_first_name.' '.$paypal_last_name;
                                                ?>
                                            </td>
                                        </tr>                                                        
                                        <tr>        
                                            <td>
                                                <strong>
                                                    <span class="glyphicon glyphicon-envelope text-primary"></span> 
                                                    Email                                                
                                                </strong>
                                            </td>
                                            <td class="text-primary">
                                                <?php echo get_option('give_when_permission_connected_person_email'); ?>  
                                            </td>
                                        </tr>                                                     
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="alert alert-info"><span class="circle_green"></span><span>You are already connected to PayPal...!!</span></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <a class="btn btn-info btn-lg" href="<?php echo site_url().'/wp-admin/?page=give_when_disconnect_paypal&action=true'; ?>">Disconnect</a>
                        </div>     
                    </div>
                </div>
            </div>
        <?php
        }
        else {
        ?>    
        <div class="wrap">
            <div class="container-fluid">
                <div class="alert alert-warning" id="connect_paypal_error" style="display: none">
                    <p id="connect_paypal_error_p"></p>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="alert alert-info"><span class="circle_red"></span><span>You are not connected to PayPal.</span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="form-table" id="give_when_callback_url">
                            <tbody>
                                <tr valign="top">
                                    <td>
                                       <img name="angelleye_connect_to_paypal" id="angelleye_connect_to_paypal" src="<?php echo GW_PLUGIN_URL; ?>/admin/images/paypal_connect.png"  style="cursor: pointer"/>
                                    </td>
                                </tr>                        
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>    
<?php
        }
        ?> 
<div class="wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">                
                
                <div id="overlay" style=" background: #f6f6f6;opacity: 0.7;width: 100%;float: left;height: 100%;position: fixed;top: 0;z-index: 1031;text-align: center; display: none;">
                    <div style="display: table; width:100%; height: 100%;">                
                        <div style="display: table-cell;vertical-align: middle;"><img src="<?php echo GW_PLUGIN_URL; ?>/admin/images/loading.gif"  style=" position: relative;top: 50%;"/>
                        <h2>Please Don't Go back , We are redirecting you to PayPal.</h2></div>
                    </div>            
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="div_log_settings">
                    <form id="give_when_integration_form_general" enctype="multipart/form-data" action="" method="post">
                        <table class="form-table">
                            <tbody>
                                <?php $Html_output->init($genral_setting_fields); ?>
                                <p class="submit">
                                    <input type="submit" name="give_when_intigration" class="btn btn-primary" value="<?php esc_attr_e('Save Settings', 'Option'); ?>" />
                                </p>                                
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>            
        </div>
    </div>     
</div>            
<?php
    }

    /**
     * request_permission function used for request permission of third party
     * @since    0.1.0
     * @access   public
     */
    public function request_permission() {
                        
        $paypal_helper_object = new Give_When_PayPal_Helper();
        
        $PayPal = new GiveWhen_Adaptive($paypal_helper_object->get_configuration());
        // Prepare request arrays        
        $Scope = array(
            'EXPRESS_CHECKOUT',             
            'BILLING_AGREEMENT', 
            'REFERENCE_TRANSACTION', 
            'TRANSACTION_DETAILS',
            'TRANSACTION_SEARCH',
            'RECURRING_PAYMENTS',            
            'ACCESS_BASIC_PERSONAL_DATA',
            'ACCESS_ADVANCED_PERSONAL_DATA',
            'REFUND'            
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
    
     /**
     * give_when_general_setting_save_field function used for save general setting field value
     * @since 1.0.0
     * @access public static
     * 
     */
    public static function give_when_connect_to_paypal_setting_save_field() {
        $givewhen_setting_fields = self::give_when_connect_to_paypal_setting_fields();
        $Html_output = new AngellEYE_Give_When_Html_output();
        $Html_output->save_fields($givewhen_setting_fields);
        if (isset($_POST['give_when_intigration'])):
            ?>
        <br><div id="setting-error-settings_updated" class="alert alert-success"> 
                <p><?php echo '<strong>' . __('Settings were saved successfully.', 'angelleye_give_when') . '</strong>'; ?></p></div>

            <?php
        endif;
    }
}

AngellEYE_Give_When_PayPal_Connect_Setting::init();
