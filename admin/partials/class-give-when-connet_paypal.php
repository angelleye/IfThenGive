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
        add_action('wp_ajax_sandbox_enabled', array(__CLASS__, 'sandbox_enabled'));
        add_action("wp_ajax_nopriv_sandbox_enabled", array(__CLASS__, 'sandbox_enabled'));
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
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options_setting');
        return $fields;
    }

    public static function give_when_connect_to_paypal_create_setting() {
        
        $conncet_to_paypal_flag = get_option('give_when_permission_connected_to_paypal');
        $genral_setting_fields = self::give_when_connect_to_paypal_setting_fields();
        $Html_output = new AngellEYE_Give_When_Html_output();
        if ($conncet_to_paypal_flag == 'Yes') {
            $label = 'You are Connected With PayPal';
            $labelClass = 'label-success';
        } else {
            $label = 'You are not Connected With PayPal';
            $labelClass = 'label-danger';
        }
        $sanbox_enable = get_option('sandbox_enable_give_when');
        if ($sanbox_enable === 'yes') {
            $sandbox_class = "";
            $live_class="gw_hide_live_class";
        } else {
            $sandbox_class = "gw_hide_sandbox_class";
            $live_class="";
        }
        $sandbox_api_user_name = get_option('give_when_sandbox_api_credentials_api_user_name');
        $sandbox_api_password = get_option('give_when_sandbox_api_credentials_api_password');
        $sandbox_signature = get_option('give_when_sandbox_api_credentials_signature');
        
        $live_api_user_name = get_option('give_when_live_api_credentials_api_user_name');
        $live_api_password = get_option('give_when_live_api_credentials_api_password');
        $live_signature = get_option('give_when_live_api_credentials_signature');
        ?>                
        <div class="wrap">
            <div class="container-fluid">
                <div class="alert alert-warning" id="connect_paypal_error" style="display: none">
                    <p id="connect_paypal_error_p"></p>
                </div>                
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading gw-panel-heading">
                                <h3 class="panel-title"><img class="pull-right" width="135" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_200x51.png" alt="PayPal Logo"></h3>
                                <div class="pull-right"><span class="label <?php echo $labelClass; ?>"><?php _e($label, 'angelleye_give_when'); ?></span></div>
                            </div>
                            <div class="panel-body">
                                <?php 
                                $success_notice = get_option('give_when_permission_connect_to_paypal_success_notice');
                                if ($success_notice) {
                                    echo '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12"><div class="alert alert-success">';
                                    echo "<strong>" . __($success_notice, 'angelleye_give_when') . "</strong>";
                                    echo '</div></div></div>';
                                    delete_option('give_when_permission_connect_to_paypal_success_notice');
                                }

                                $failed_notice = get_option('give_when_permission_connect_to_paypal_failed_notice');
                                if ($failed_notice) {
                                    echo '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12"><div class="alert alert-danger">';
                                    echo "<strong>" . __($failed_notice, 'angelleye_give_when') . "</strong>";
                                    echo '</div></div></div>';
                                    delete_option('give_when_permission_connect_to_paypal_failed_notice');
                                }
                                ?>
                                <?php
                                if ($conncet_to_paypal_flag == 'Yes') {
                                ?>                    
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="table-responsive">
                                                <table class="table table-responsive">
                                                    <tr>        
                                                        <td>
                                                            <strong>                                                            
                                                                <?php echo __('PayPal Account ID', 'angelleye_give_when'); ?>
                                                            </strong>
                                                        </td>
                                                        <td class="text-primary">
                                                            <?php
                                                            $paypal_account_id = get_option('give_when_permission_connected_person_merchant_id');
                                                            echo isset($paypal_account_id) ? $paypal_account_id : '';
                                                            ?>
                                                        </td>
                                                    </tr>                                                
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-sm-12">
                                            <a class="btn btn-info" href="<?php echo site_url() . '/wp-admin/?page=give_when_disconnect_paypal&action=true'; ?>">Disconnect</a>
                                        </div>
                                    </div>
                                <?php } ?>
                                <form id="give_when_integration_form_general" enctype="multipart/form-data" action="" method="post">
                                <div class="div_log_settings">                                    
                                        <table class="form-table">
                                            <tbody>
                                                <?php $Html_output->init($genral_setting_fields); ?>                                                
                                            </tbody>
                                        </table>                                                                        
                                </div>
                                <div id="give_when_sandbox_fields" class="<?php echo $sandbox_class; ?>">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <?php                                           
                                            if ($conncet_to_paypal_flag != 'Yes') {                                
                                                $sandbox = 'true';
                                                $url = 'http://angelleye.project-demo.info/paypal/';
                                                $return_url = site_url('?action=permission_callback');
                                                $postData = "sandbox={$sandbox}&api=connect_to_paypal&return_url={$return_url}";
                                                $log_sandbox_connect = array(
                                                    "sandbox" => $sandbox,
                                                    "postdata" => $postData,                                                    
                                                );
                                                //save log
                                                $debug = (get_option('log_enable_give_when') == 'yes') ? 'yes' : 'no';
                                                if ('yes' == $debug) {
                                                    $log_write = new AngellEYE_Give_When_Logger();
                                                    $log_write->add('angelleye_give_when_connect_to_paypal', 'Connect With Facebook RequestData : ' . print_r($log_sandbox_connect, true), 'connect_to_paypal');
                                                }
                                                $ConnectPayPalJson = self::curl_request($url, $postData);
                                                $ConnectPayPalArray = json_decode($ConnectPayPalJson, true);
                                                //save log
                                                $debug = (get_option('log_enable_give_when') == 'yes') ? 'yes' : 'no';
                                                if ('yes' == $debug) {
                                                    $log_write = new AngellEYE_Give_When_Logger();
                                                    $log_write->add('angelleye_give_when_connect_to_paypal', 'Connect With Facebook ResponseData : ' . print_r($ConnectPayPalArray, true), 'connect_to_paypal');
                                                }
                                                if ($ConnectPayPalArray['ACK'] == 'success') {
                                                    ?>                                                                          
                                                    <div class="form-group">
                                                        <a data-toggle="tooltip" data-original-title="Connect with PayPal" data-paypal-button="true" href="<?php echo $ConnectPayPalArray['action_url']; ?>&displayMode=minibrowser" target="PPFrame" class="btn btn-primary">Connect with PayPal</a>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="alert alert-warning" id="connect_with_paypal_error">
                                                        <p id="connect_with_paypal_error_p">Error : <?php echo $ConnectPayPalArray['DATA']['error']; ?></p>
                                                        <p id="connect_with_paypal_error_desc">Error : <?php echo $ConnectPayPalArray['DATA']['error_description']; ?></p>
                                                    </div>
                                                <?php }
                                            }
                                            ?>
                                        <a class="btn btn-sm btn-default" data-toggle="collapse" href="#gwsandboxClass" aria-expanded="false" aria-controls="gwsandboxClass" id="gwsandbox_details">Show Advanced Details</a><br><br>
                                        </div>                                                                                                                        
                                        <div class="col-lg-12 col-md-12 col-sm-12 collapse" id="gwsandboxClass">
                                            <div class="form-group">
                                                <label for="SandboxAPIUserName"><?php _e('Sandbox API User Name','angelleye_give_when'); ?></label>
                                                <input type="text" class="form-control" id="give_when_sandbox_api_credentials_api_user_name" name="give_when_sandbox_api_credentials_api_user_name" value="<?php echo isset($sandbox_api_user_name) ? $sandbox_api_user_name : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="SandboxAPIPassword"><?php _e('Sandbox API Password','angelleye_give_when'); ?></label>
                                                <input type="password" class="form-control" id="give_when_sandbox_api_credentials_api_password" name="give_when_sandbox_api_credentials_api_password" value="<?php echo isset($sandbox_api_password) ? $sandbox_api_password : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="SandboxAPISignature"><?php _e('Sandbox API Signature','angelleye_give_when'); ?></label>
                                                <input type="password" class="form-control" id="give_when_sandbox_api_credentials_signature" name="give_when_sandbox_api_credentials_signature" value="<?php echo isset($sandbox_signature) ? $sandbox_signature : ''; ?>">
                                            </div>                                                                                                                
                                        </div>
                                    </div>
                                </div>
                                <div id="give_when_live_fields"  class="<?php echo $live_class; ?>">
                                    <div class="row">                                        
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <?php                                            
                                            if ($conncet_to_paypal_flag != 'Yes') {                                
                                                $sandbox = 'false';
                                                $url = 'http://angelleye.project-demo.info/paypal/';
                                                $return_url = site_url('?action=permission_callback');
                                                $postData = "sandbox={$sandbox}&api=connect_to_paypal&return_url={$return_url}";                                                
                                                $ConnectPayPalJson = self::curl_request($url, $postData);
                                                $ConnectPayPalArray = json_decode($ConnectPayPalJson, true);
                                                if ($ConnectPayPalArray['ACK'] == 'success') {
                                                    ?>                                                                          
                                                    <div class="form-group">
                                                        <a data-toggle="tooltip" data-original-title="Connect with PayPal" data-paypal-button="true" href="<?php echo $ConnectPayPalArray['action_url']; ?>&displayMode=minibrowser" target="PPFrame" class="btn btn-primary">Connect with PayPal</a>
                                                    </div>
                                                    <?php
                                                } else {
                                                    $error = json_decode($ConnectPayPalArray['DATA'], true);
                                                    ?>
                                                    <div class="alert alert-warning" id="connect_with_paypal_error">
                                                        <p id="connect_with_paypal_error_p">Error : <?php echo $error['error']; ?></p>
                                                        <p id="connect_with_paypal_error_desc">Error : <?php echo $error['error_description']; ?></p>
                                                    </div>
                                                <?php }
                                            }
                                            ?>
                                            <a class="btn btn-sm btn-default" data-toggle="collapse" href="#gwliveClass" aria-expanded="false" aria-controls="gwliveClass" id="gwlive_details">Show Advanced Details</a><br><br>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 collapse" id="gwliveClass">
                                            <div class="form-group">
                                                <label for="APIUserName"><?php _e('API User Name','angelleye_give_when'); ?></label>
                                                <input type="text" class="form-control" id="give_when_sandbox_api_credentials_api_user_name" name="give_when_sandbox_api_credentials_api_user_name" value="<?php echo isset($live_api_user_name) ? $live_api_user_name : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="APIPassword"><?php _e('API Password','angelleye_give_when'); ?></label>
                                                <input type="password" class="form-control" id="give_when_sandbox_api_credentials_api_password" name="give_when_sandbox_api_credentials_api_password" value="<?php echo isset($live_api_password) ? $live_api_password : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="APISignature"><?php _e('API Signature','angelleye_give_when'); ?></label>
                                                <input type="password" class="form-control" id="give_when_sandbox_api_credentials_signature" name="give_when_sandbox_api_credentials_signature" value="<?php echo isset($live_signature) ? $live_signature : ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="selectCurrency"><?php _e('Select Currency','angelleye_give_when'); ?></label>;
                                                <?php                                               
                                                    $PayPal = new \angelleye\PayPal\PayPal();
                                                    $ccode = get_option('gw_currency_code');
                                                ?>
                                                <select class="form-control" name="gw_currency_code">
                                                    <option value=""><?php _e('Select Currency','angelleye_give_when'); ?></option>
                                                   <?php
                                                        foreach ($PayPal->CurrencyCodes as $Key => $Value) {
                                                            if($ccode == $Key){
                                                                echo '<option value="'.$Key.'" selected>'.$Value.'</option>';
                                                            }
                                                            else{
                                                                echo '<option value="'.$Key.'">'.$Value.'</option>';
                                                            }
                                                            
                                                        }
                                                   ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>                                    
                                <div class="checkbox">
                                    <?php
                                        $checkbox = get_option('log_enable_give_when');
                                        if($checkbox == 'yes'){
                                            $checkbox_checked = "checked";
                                        }
                                        else{
                                            $checkbox_checked = "";
                                        }
                                    ?>
                                    <label><input type="checkbox" name="log_enable_give_when" id="log_enable_give_when" <?php echo $checkbox_checked; ?> ><?php _e('Save Logs for GiveWhen.','angelleye_give_when'); ?></label>
                                </div>  
                                <p class="submit">
                                    <input type="submit" name="give_when_intigration" class="btn btn-primary" value="<?php esc_attr_e('Save Settings', 'Option'); ?>" />
                                </p>
                                </form>
                            </div>
                        </div>
                    </div>                    
                </div>
                <div dir="ltr" style="text-align: left;" trbidi="on"></div>
                <script>(function (d, s, id) {
                        var js, ref = d.getElementsByTagName(s)[0];
                        if (!d.getElementById(id)) {
                            js = d.createElement(s);
                            js.id = id;
                            js.async = true;
                            js.src =
                                    "https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js"
                                    ;
                            ref.parentNode.insertBefore(js, ref);
                        }
                    }(document, "script", "paypal-js"));
                </script>
            </div>
        </div>
        <?php
    }

    public function sandbox_enabled() {
        if (isset($_POST['sandbox'])) {
            $sandbox = $_POST['sandbox'];
            if ($sandbox == 'true') {
                update_option('sandbox_enable_give_when', 'yes');
            } else {
                update_option('sandbox_enable_give_when', 'no');
            }
            echo json_encode(array('Ack' => 'success'));
        }
        exit;
    }

    public static function curl_request($url, $postData) {
        $httpHeaders = array(
            'Content-Type:application/x-www-form-urlencoded'
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        $result = curl_exec($ch);
        return $result;
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
            if(isset($_POST['gw_currency_code'])){
                update_option('gw_currency_code', $_POST['gw_currency_code']);
            }
            else{
                update_option('gw_currency_code', 'USD');
            }
            if(isset($_POST['log_enable_give_when'])){
                update_option('log_enable_give_when', 'yes');
            }
            else{
                update_option('log_enable_give_when', 'no');
            }
            ?>
            <br><div id="setting-error-settings_updated" class="alert alert-success"> 
                <p><?php echo '<strong>' . __('Settings were saved successfully.', 'angelleye_give_when') . '</strong>'; ?></p></div>

            <?php
        endif;
    }

}

AngellEYE_Give_When_PayPal_Connect_Setting::init();
