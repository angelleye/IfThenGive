<?php

/**
 * This class defines all code necessary to Companies Setting from admin side
 * @class       AngellEYE_IfThenGive_PayPal_Connect_Setting
 * @version	1.0.0
 * @package     IfThenGive
 * @subpackage  IfThenGive/admin/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_PayPal_Connect_Setting {

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
        add_action('ifthengive_connect_to_paypal_create_setting', array(__CLASS__, 'ifthengive_connect_to_paypal_create_setting'));
        add_action('ifthengive_connect_to_paypal_setting_save_field', array(__CLASS__, 'ifthengive_connect_to_paypal_setting_save_field'));                       
        add_action('wp_ajax_sandbox_enabled', array(__CLASS__, 'sandbox_enabled'));
        add_action("wp_ajax_nopriv_sandbox_enabled", array(__CLASS__, 'sandbox_enabled'));
    }

    public static function ifthengive_connect_to_paypal_setting_fields() {
        global $wpdb;
        $Logger = new AngellEYE_IfThenGive_Logger();       
        $fields[] = array(
            'title' => __('Sandbox', ITG_TEXT_DOMAIN),
            'id' => 'itg_sandbox_enable',
            'type' => 'checkbox',
            'label' => __('Enable logging', ITG_TEXT_DOMAIN),
            'default' => 'no',
            'labelClass' => 'switch',
            'desc' => sprintf(__('<div class="slider round"></div>', ITG_TEXT_DOMAIN))
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options_setting');
        return $fields;
    }

    public static function ifthengive_connect_to_paypal_create_setting() {
                
        $connect_to_sandbox_paypal_flag = get_option('itg_sb_connected_to_paypal');
        $connect_to_live_paypal_flag = get_option('itg_live_connected_to_paypal');
        
        $general_setting_fields = self::ifthengive_connect_to_paypal_setting_fields();
        $Html_output = new AngellEYE_IfThenGive_Html_output();
        
        $sanbox_enable = get_option('itg_sandbox_enable');
        if ($sanbox_enable === 'yes') {
            $sandbox_class = "";
            $live_class="itg_hide_live_class";
        } else {
            $sandbox_class = "itg_hide_sandbox_class";
            $live_class="";
        }
        $sandbox_api_user_name = get_option('itg_sb_api_credentials_username');
        $sandbox_api_password = get_option('itg_sb_api_credentials_password');
        $sandbox_signature = get_option('itg_sb_api_credentials_signature');
        
        $live_api_user_name = get_option('itg_lv_api_credentials_username');
        $live_api_password = get_option('itg_lv_api_credentials_password');
        $live_signature = get_option('itg_lv_api_credentials_signature');
        
        $itg_sb_add_manually = get_option('itg_sb_api_credentials_addded_manually');
        $itg_lv_add_manually = get_option('itg_lv_api_credentials_addded_manually');
        
        ?>                
        <div class="wrap">
            <div class="container-fluid">
                <div class="alert alert-warning" id="connect_paypal_error" style="display: none">
                    <p id="connect_paypal_error_p"></p>
                </div>                  
                    <div id="overlay" style=" background: #d9d9da;opacity: 0.9;width: 100%;float: left;height: 100%;position: fixed;top: 0;left:0;right:0;z-index: 1031;text-align: center; display: none;">
                        <div class="itg_loader"></div>
                        <h1 style="font-weight: 600;"><?php _e('Processing...',ITG_TEXT_DOMAIN); ?></h1>                    
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <form id="ifthengive_integration_form_general" enctype="multipart/form-data" action="" method="post">
                        <div class="panel panel-default">
                            <div class="panel-heading itg-panel-heading">
                                <h3 class="panel-title">
                                    <img class="pull-right"  src="<?php echo ITG_PLUGIN_URL.'admin/images/PayPal_IfThenGive.png' ?>" alt="PayPal Logo">
                                    <div class="itg_div_log_settings">                                    
                                        <table class="form-table">
                                            <tbody>
                                                <?php $Html_output->init($general_setting_fields); ?>                                                
                                            </tbody>
                                        </table>                                                                        
                                </div>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <?php 
                                $success_notice = get_option('itg_permission_connect_to_paypal_success_notice');
                                if ($success_notice) {
                                    echo '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12"><div class="alert alert-success">';
                                    echo "<strong>" . __($success_notice, ITG_TEXT_DOMAIN) . "</strong>";
                                    echo '</div></div></div>';
                                    delete_option('itg_permission_connect_to_paypal_success_notice');
                                }

                                $failed_notice = get_option('itg_permission_connect_to_paypal_failed_notice');
                                if ($failed_notice) {
                                    echo '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12"><div class="alert alert-danger">';
                                    echo "<strong>" . __($failed_notice, ITG_TEXT_DOMAIN) . "</strong>";
                                    echo '</div></div></div>';
                                    delete_option('itg_permission_connect_to_paypal_failed_notice');
                                }
                                ?>                                                                                                    
                                
                                <div id="ifthengive_sandbox_fields" class="<?php echo $sandbox_class; ?>">
                                <?php
                                    $sb_paypal_account_id = get_option('itg_permission_sb_connected_person_merchant_id');                                    
                                    if ($connect_to_sandbox_paypal_flag == 'Yes') {
                                ?>
                                    <div class="row">
                                        <?php 
                                            if ($sb_paypal_account_id !== false && !empty($sb_paypal_account_id)) {
                                        ?>
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="table-responsive">
                                                <table class="table table-responsive">
                                                    <tr>        
                                                        <td><strong><?php echo __('PayPal Account ID', ITG_TEXT_DOMAIN); ?></strong></td>
                                                        <td class="text-primary"><?php echo $sb_paypal_account_id; ?></td>
                                                    </tr>                                                
                                                </table>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                            <a class="btn btn-info" href="<?php echo site_url() . '/wp-admin/?page=ifthengive_disconnect_paypal&action=true&env=sandbox'; ?>"><?php _e('Disconnect',ITG_TEXT_DOMAIN); ?></a>
                                        </div>                                        
                                    </div>                                    
                                    <div class="clearfix"></div>
                                <?php } ?>
                                    <div class="row">                                        
                                         <div class="col-md-6 col-lg-6 col-sm-6">
                                            <?php
                                            if ($sb_paypal_account_id !== false && $connect_to_sandbox_paypal_flag == 'Yes') {
                                                echo '<span class="label label-success">'.__('You are Connected with SandBox PayPal Environment.',ITG_TEXT_DOMAIN).'</span><br><br><br>';
                                            }
                                            else if($connect_to_sandbox_paypal_flag == 'Yes'){
                                                echo '<br><span class="label label-warning">'.__('You are Partially Connected with SandBox PayPal Environment.',ITG_TEXT_DOMAIN).'</span><br><br><br>';                                               
                                            }
                                            else{
                                                echo '<span class="label label-danger">'.__('You are not Connected with SandBox PayPal Environment.',ITG_TEXT_DOMAIN).'</span><br><br><br>';
                                            }
                                            if($itg_sb_add_manually !== false  && $itg_sb_add_manually ==='Yes' && $sb_paypal_account_id === false){
                                                $collpase_class = 'in';
                                                $itg_sb_button_text = __('Hide Advanced Details',ITG_TEXT_DOMAIN);
                                                $checkbox_sb_manually = 'checked';
                                                $sb_disabled = '';
                                            }
                                            else{
                                                $collpase_class = '';
                                                $itg_sb_button_text = __('Show Advanced Details',ITG_TEXT_DOMAIN);
                                                $checkbox_sb_manually = '';
                                                $sb_disabled = 'disabled';
                                            }
                                            ?>
                                        </div>                                           
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <?php                                           
                                            if ($sb_paypal_account_id == false && $itg_sb_add_manually !== 'Yes') {                                
                                                $sandbox = 'true';
                                                $url = ITG_ISU_URL;
                                                $return_url = site_url('?action=permission_callback');
                                                $postData = "sandbox={$sandbox}&api=connect_to_paypal&return_url={$return_url}";
                                                $log_sandbox_connect = array(
                                                    "sandbox" => $sandbox,
                                                    "postdata" => $postData,                                                    
                                                );
                                                //save log
                                                $debug = (get_option('itg_log_enable') == 'yes') ? 'yes' : 'no';
                                                if ('yes' == $debug) {
                                                    $log_write = new AngellEYE_IfThenGive_Logger();
                                                    $log_write->add('angelleye_itg_connect_to_paypal', 'Connect With PayPal RequestData : ' . print_r($log_sandbox_connect, true), 'connect_to_paypal');
                                                }
                                                $ConnectPayPalJson = self::curl_request($url, $postData);
                                                $ConnectPayPalArray = json_decode($ConnectPayPalJson, true);
                                                //save log
                                                $debug = (get_option('itg_log_enable') == 'yes') ? 'yes' : 'no';
                                                if ('yes' == $debug) {
                                                    $log_write = new AngellEYE_IfThenGive_Logger();
                                                    $log_write->add('angelleye_itg_connect_to_paypal', 'Connect With PayPal ResponseData : ' . print_r($ConnectPayPalArray, true), 'connect_to_paypal');
                                                }
                                                if ($ConnectPayPalArray['ACK'] == 'success') {
                                                    ?>                                                                          
                                                    <div class="form-group">
                                                        <a id="itg_connect_with_paypal_sb" data-toggle="tooltip" data-original-title="Connect with PayPal" data-paypal-button="true" href="<?php echo $ConnectPayPalArray['action_url']; ?>&displayMode=minibrowser" target="PPFrame" class="btn btn-primary">Connect with PayPal</a>
                                                    </div>
                                                    <?php
                                                } else {                                                    
                                                    $error = json_decode($ConnectPayPalArray['DATA'], true);
                                                    if(isset($error['error']) || isset($error['error_description'])){                                                                                                            
                                                    ?>
                                                    <div class="alert alert-warning" id="connect_with_paypal_error">
                                                        <p><?php _e('PayPal Error',ITG_TEXT_DOMAIN); ?></p>
                                                        <p id="connect_with_paypal_error_p"><?php _e('Error :',ITG_TEXT_DOMAIN); ?> <?php echo isset($error['error']) ? $error['error'] : ''; ?></p>
                                                        <p id="connect_with_paypal_error_desc"><?php _e('Error :',ITG_TEXT_DOMAIN); ?> <?php echo isset($error['error_description']) ? $error['error_description'] : '' ; ?></p>
                                                    </div>
                                                    <?php }
                                                    if(isset($ConnectPayPalArray['DATA']['RAWRESPONSE']['name']) || isset($ConnectPayPalArray['DATA']['RAWRESPONSE']['message'])){ ?>
                                                        <div class="alert alert-warning" id="connect_with_paypal_error">
                                                            <p><?php _e('PayPal Error',ITG_TEXT_DOMAIN); ?></p>
                                                            <p id="connect_with_paypal_error_p"><?php _e('Error :',ITG_TEXT_DOMAIN); ?> <?php echo $ConnectPayPalArray['DATA']['RAWRESPONSE']['name']; ?></p>
                                                            <p id="connect_with_paypal_error_desc"><?php _e('Error :',ITG_TEXT_DOMAIN); ?> <?php echo $ConnectPayPalArray['DATA']['RAWRESPONSE']['message']; ?></p>
                                                        </div>
                                                    <?php                                                    
                                                    }
                                                }
                                            }
                                            ?>
                                            <a class="btn btn-sm btn-default" data-toggle="collapse" href="#itgsandboxClass" aria-expanded="false" aria-controls="itgsandboxClass" id="itgsandbox_details"><?php echo $itg_sb_button_text; ?></a><br><br>
                                        </div>                                                                                                                        
                                        <div class="col-lg-12 col-md-12 col-sm-12 collapse <?php echo $collpase_class; ?>" id="itgsandboxClass">                                            
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="itg_sandbox_add_manually" id="itg_sandbox_add_manually" <?php echo $checkbox_sb_manually; ?> ><?php _e('Add Sandbox Credentials Manually.',ITG_TEXT_DOMAIN); ?>
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="SandboxAPIUserName"><?php _e('Sandbox API User Name',ITG_TEXT_DOMAIN); ?></label>
                                                <input type="text" class="form-control" id="itg_sb_api_credentials_username" name="itg_sb_api_credentials_username" value="<?php echo isset($sandbox_api_user_name) ? esc_attr($sandbox_api_user_name,ITG_TEXT_DOMAIN) : ''; ?>" <?php echo $sb_disabled; ?>>
                                            </div>
                                            <div class="form-group">
                                                <label for="SandboxAPIPassword"><?php _e('Sandbox API Password',ITG_TEXT_DOMAIN); ?></label>
                                                <input type="password" class="form-control" id="itg_sb_api_credentials_password" name="itg_sb_api_credentials_password" value="<?php echo isset($sandbox_api_password) ? esc_attr($sandbox_api_password,ITG_TEXT_DOMAIN) : ''; ?>" <?php echo $sb_disabled; ?>>
                                            </div>
                                            <div class="form-group">
                                                <label for="SandboxAPISignature"><?php _e('Sandbox API Signature',ITG_TEXT_DOMAIN); ?></label>
                                                <input type="password" class="form-control" id="itg_sb_api_credentials_signature" name="itg_sb_api_credentials_signature" value="<?php echo isset($sandbox_signature) ? esc_attr($sandbox_signature,ITG_TEXT_DOMAIN) : ''; ?>" <?php echo $sb_disabled; ?>>
                                            </div>                                                                                                                
                                        </div>
                                    </div>
                                </div>
                                <div id="ifthengive_live_fields"  class="<?php echo $live_class; ?>">
                                    <?php
                                        $live_paypal_account_id = get_option('itg_permission_lv_connected_person_merchant_id');
                                        if ($connect_to_live_paypal_flag == 'Yes') {
                                    ?>
                                    <div class="row">
                                        <?php 
                                            if ($live_paypal_account_id !== false && !empty($live_paypal_account_id)) {
                                        ?>
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="table-responsive">
                                                <table class="table table-responsive">
                                                    <tr>        
                                                        <td><strong><?php echo __('PayPal Account ID', ITG_TEXT_DOMAIN); ?></strong></td>
                                                        <td class="text-primary"><?php echo $live_paypal_account_id; ?></td>
                                                    </tr>                                                
                                                </table>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                            <a class="btn btn-info" href="<?php echo site_url() . '/wp-admin/?page=ifthengive_disconnect_paypal&action=true&env=live'; ?>">Disconnect</a>
                                        </div>
                                    </div>                                    
                                    <div class="clearfix"></div>
                                <?php } ?>
                                    <div class="row">                                        
                                         <div class="col-md-6 col-lg-6 col-sm-6">
                                            <?php 
                                            if ($live_paypal_account_id !== false && $connect_to_live_paypal_flag == 'Yes') {
                                                echo '<span class="label label-success">'.__('You are Connected with Live PayPal Environment.',ITG_TEXT_DOMAIN).'</span><br><br><br>';
                                            }
                                            else if($connect_to_live_paypal_flag == 'Yes'){
                                                echo '<br><span class="label label-warning">'.__('You are Partially Connected with Live PayPal Environment.',ITG_TEXT_DOMAIN).'</span><br><br><br>';                                               
                                            }
                                            else{
                                                echo '<span class="label label-danger">'.__('You are not Connected with Live PayPal Environment.',ITG_TEXT_DOMAIN).'</span><br><br><br>';
                                            }
                                            if($itg_lv_add_manually !== false  && $itg_lv_add_manually ==='Yes' && $live_paypal_account_id === false){
                                                $lv_collpase_class = 'in';
                                                $itg_lv_button_text = __('Hide Advanced Details',ITG_TEXT_DOMAIN);
                                                $checkbox_lv_manually = 'checked';
                                                 $lv_disabled = '';
                                            }
                                            else{
                                                $lv_collpase_class = '';
                                                $itg_lv_button_text = __('Show Advanced Details',ITG_TEXT_DOMAIN);
                                                $checkbox_lv_manually = '';
                                                $lv_disabled = 'disabled';
                                            }
                                            ?>
                                        </div>                                           
                                    </div>
                                    <div class="row">                                        
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <?php                                            
                                            if ($live_paypal_account_id == false && $itg_lv_add_manually !== 'Yes') {
                                                $sandbox = 'false';
                                                $url = ITG_ISU_URL;
                                                $return_url = site_url('?action=permission_callback');
                                                $postData = "sandbox={$sandbox}&api=connect_to_paypal&return_url={$return_url}";                                                
                                                $log_live_connect = array(
                                                    "sandbox" => $sandbox,
                                                    "postdata" => $postData,                                                    
                                                );
                                                //save log
                                                $debug = (get_option('itg_log_enable') == 'yes') ? 'yes' : 'no';
                                                if ('yes' == $debug) {
                                                    $log_write = new AngellEYE_IfThenGive_Logger();
                                                    $log_write->add('angelleye_itg_connect_to_paypal', 'Connect With PayPal RequestData : ' . print_r($log_live_connect, true), 'connect_to_paypal');
                                                }
                                                $ConnectPayPalJson = self::curl_request($url, $postData);
                                                $ConnectPayPalArray = json_decode($ConnectPayPalJson, true);
                                                //save log
                                                $debug = (get_option('itg_log_enable') == 'yes') ? 'yes' : 'no';
                                                if ('yes' == $debug) {
                                                    $log_write = new AngellEYE_IfThenGive_Logger();
                                                    $log_write->add('angelleye_itg_connect_to_paypal', 'Connect With PayPal ResponseData : ' . print_r($ConnectPayPalArray, true), 'connect_to_paypal');
                                                }
                                                if ($ConnectPayPalArray['ACK'] == 'success') {
                                                    ?>                                                                          
                                                    <div class="form-group">
                                                        <a id="itg_connect_with_paypal_live" data-toggle="tooltip" data-original-title="Connect with PayPal" data-paypal-button="true" href="<?php echo $ConnectPayPalArray['action_url']; ?>&displayMode=minibrowser" target="PPFrame" class="btn btn-primary">Connect with PayPal</a>
                                                    </div>
                                                    <?php
                                                } else {                                                    
                                                    $error = json_decode($ConnectPayPalArray['DATA'], true);
                                                    if(isset($error['error']) || isset($error['error_description'])){                                                                                                            
                                                    ?>
                                                    <div class="alert alert-warning" id="connect_with_paypal_error">
                                                        <p><?php _e('PayPal Error',ITG_TEXT_DOMAIN); ?></p>
                                                        <p id="connect_with_paypal_error_p"><?php _e('Error :',ITG_TEXT_DOMAIN); ?> <?php echo isset($error['error']) ? $error['error'] : ''; ?></p>
                                                        <p id="connect_with_paypal_error_desc"><?php _e('Error :',ITG_TEXT_DOMAIN); ?> <?php echo isset($error['error_description']) ? $error['error_description'] : '' ; ?></p>
                                                    </div>
                                                    <?php }
                                                    if(isset($ConnectPayPalArray['DATA']['RAWRESPONSE']['name']) || isset($ConnectPayPalArray['DATA']['RAWRESPONSE']['message'])){ ?>
                                                        <div class="alert alert-warning" id="connect_with_paypal_error">
                                                            <p><?php _e('PayPal Error',ITG_TEXT_DOMAIN); ?></p>
                                                            <p id="connect_with_paypal_error_p"><?php _e('Error :',ITG_TEXT_DOMAIN); ?> <?php echo $ConnectPayPalArray['DATA']['RAWRESPONSE']['name']; ?></p>
                                                            <p id="connect_with_paypal_error_desc"><?php _e('Error :',ITG_TEXT_DOMAIN); ?> <?php echo $ConnectPayPalArray['DATA']['RAWRESPONSE']['message']; ?></p>
                                                        </div>
                                                    <?php                                                    
                                                    }
                                                }
                                            }
                                            ?>
                                            <a class="btn btn-sm btn-default" data-toggle="collapse" href="#itgliveClass" aria-expanded="false" aria-controls="itgliveClass" id="itglive_details"><?php echo $itg_lv_button_text; ?></a><br><br>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 collapse <?php echo $lv_collpase_class; ?>" id="itgliveClass">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="itg_live_add_manually" id="itg_live_add_manually" <?php echo $checkbox_lv_manually; ?> ><?php _e('Add Live Credentials Manually.',ITG_TEXT_DOMAIN); ?>
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="APIUserName"><?php _e('API User Name',ITG_TEXT_DOMAIN); ?></label>
                                                <input type="text" class="form-control" id="itg_lv_api_credentials_username" name="itg_lv_api_credentials_username" value="<?php echo isset($live_api_user_name) ? esc_attr($live_api_user_name,ITG_TEXT_DOMAIN) : ''; ?>" <?php echo $lv_disabled; ?>>
                                            </div>
                                            <div class="form-group">
                                                <label for="APIPassword"><?php _e('API Password',ITG_TEXT_DOMAIN); ?></label>
                                                <input type="password" class="form-control" id="itg_lv_api_credentials_password" name="itg_lv_api_credentials_password" value="<?php echo isset($live_api_password) ? esc_attr($live_api_password,ITG_TEXT_DOMAIN) : ''; ?>" <?php echo $lv_disabled; ?>>
                                            </div>
                                            <div class="form-group">
                                                <label for="APISignature"><?php _e('API Signature',ITG_TEXT_DOMAIN); ?></label>
                                                <input type="password" class="form-control" id="itg_lv_api_credentials_signature" name="itg_lv_api_credentials_signature" value="<?php echo isset($live_signature) ? esc_attr($live_signature,ITG_TEXT_DOMAIN) : ''; ?>" <?php echo $lv_disabled; ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="selectCurrency"><?php _e('Select Currency',ITG_TEXT_DOMAIN); ?></label>;
                                                <?php                                       
                                                    $PayPal_config = new AngellEYE_IfThenGive_PayPal_Helper();        
                                                    $PayPal_config->set_api_cedentials();        
                                                    $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
                                                    /*
                                                    *   By default Angell EYE PayPal PHP Library has ButtonSource is "AngellEYE_PHPClass".
                                                    *   We are overwirting that variable with "AngellEYE_IfThenGive" value.
                                                    *   It also reflactes in NVPCredentials string so we are also replcing it.
                                                    */
                                                    $PayPal->APIButtonSource = ITG_BUTTON_SOURCE;
                                                    $PayPal->NVPCredentials = str_replace('AngellEYE_PHPClass',ITG_BUTTON_SOURCE,$PayPal->NVPCredentials);
                                                    $ccode = get_option('itg_currency_code');
                                                ?>
                                                <select class="form-control" name="itg_currency_code">
                                                    <option value=""><?php _e('Select Currency',ITG_TEXT_DOMAIN); ?></option>
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="brandname"><?php _e('Brand Name',ITG_TEXT_DOMAIN);?></label>
                                            <input type="text" class="form-control" id="brandname" name="brandname" value="">
                                            <p class="help-block"><?php _e('This Brand Name will be displayed on the PayPal as sitename during checkout.',ITG_TEXT_DOMAIN);?></p>
                                        </div>
                                        <div class="form-group">
                                            <label for="itg_brandlogo"><?php _e('Upload Brand Logo',ITG_TEXT_DOMAIN); ?></label>
                                            <?php echo self::misha_image_uploader_field('itg_brandlogo',get_option( 'itg_brandlogo' )) ?>
                                            <p class="help-block"><?php _e('Logo will be on the PayPal during checkout.',ITG_TEXT_DOMAIN); ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="checkbox">
                                    <?php
                                        $checkbox = get_option('itg_log_enable');
                                        if($checkbox == 'yes'){
                                            $checkbox_checked = "checked";
                                        }
                                        else{
                                            $checkbox_checked = "";
                                        }
                                    ?>
                                    <label><input type="checkbox" name="itg_log_enable" id="itg_log_enable" <?php echo $checkbox_checked; ?> ><?php _e('Save Logs.',ITG_TEXT_DOMAIN); ?></label>
                                </div>  
                                <p class="submit">
                                    <input type="submit" name="ifthengive_intigration" class="btn btn-primary" value="<?php esc_attr_e('Save Settings', 'Option'); ?>" />
                                </p>                                
                            </div>
                        </div>
                       </form>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title"><h4 class="text-info"><?php _e('How to Connect PayPal Account',ITG_TEXT_DOMAIN); ?></h4></div>
                            </div>
                            <div class="panel-body">
                                <ol>
                                    <li><?php _e('Click the Connect with PayPal button to begin the process.',ITG_TEXT_DOMAIN); ?></li>
                                    <li><?php _e('Login to your PayPal account, or create an account if necessary.',ITG_TEXT_DOMAIN); ?></li>
                                    <li><?php _e('Review the authorization details and click the "Yes I Authorize It" button.',ITG_TEXT_DOMAIN); ?></li>
                                    <li><?php _e('Click the button to "Go back to {My Website Name}".',ITG_TEXT_DOMAIN); ?></li>
                                    <li><?php _e('Verify that your PayPal Account ID is listed and click to Save Settings.',ITG_TEXT_DOMAIN); ?></li>
                                </ol>
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
        <?php
    }
    
    public static function misha_image_uploader_field( $name, $value = '') {
	$image = ' button">Upload image';
	$image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
	$display = 'none'; // display state ot the "Remove image" button
 
	if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
 
		// $image_attributes[0] - image URL
		// $image_attributes[1] - image width
		// $image_attributes[2] - image height
 
		$image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
		$display = 'inline-block';
 
	} 
 
	return '
	<div>
		<a href="#" class="misha_upload_image_button' . $image . '</a>
		<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
		<a href="#" class="misha_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
	</div>';
    }

    public function sandbox_enabled() {
        if (isset($_POST['sandbox'])) {
            $sandbox = $_POST['sandbox'];
            if ($sandbox == 'true') {
                update_option('itg_sandbox_enable', 'yes');
            } else {
                update_option('itg_sandbox_enable', 'no');
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
     * ifthengive_connect_to_paypal_setting_save_field function used for save general setting field value
     * @since 1.0.0
     * @access public static
     * 
     */
    public static function ifthengive_connect_to_paypal_setting_save_field() {        
        $ifthengive_setting_fields = self::ifthengive_connect_to_paypal_setting_fields();
        $Html_output = new AngellEYE_IfThenGive_Html_output();
        $Html_output->save_fields($ifthengive_setting_fields);        
        if (isset($_POST['ifthengive_intigration'])): 
            if(isset($_POST['itg_sandbox_enable']) && $_POST['itg_sandbox_enable'] == '1'){
                if(isset($_POST['itg_sandbox_add_manually'])){
                    update_option('itg_sb_api_credentials_username',$_POST["itg_sb_api_credentials_username"]);
                    update_option('itg_sb_api_credentials_password',$_POST["itg_sb_api_credentials_password"]);
                    update_option('itg_sb_api_credentials_signature',$_POST["itg_sb_api_credentials_signature"]);
                    update_option('itg_sb_connected_to_paypal', 'Yes');
                    update_option('itg_sb_api_credentials_addded_manually','Yes');
                }
            }
            else{
                if(isset($_POST['itg_live_add_manually'])){                
                    update_option('itg_lv_api_credentials_username',$_POST['itg_lv_api_credentials_username']);
                    update_option('itg_lv_api_credentials_password',$_POST['itg_lv_api_credentials_password']);
                    update_option('itg_lv_api_credentials_signature',$_POST['itg_lv_api_credentials_signature']);
                    update_option('itg_live_connected_to_paypal', 'Yes');
                    update_option('itg_lv_api_credentials_addded_manually','Yes');
                }
            }
            if(isset($_POST['itg_currency_code'])){
                update_option('itg_currency_code', $_POST['itg_currency_code']);
            }
            else{
                update_option('itg_currency_code', 'USD');
            }
            if(isset($_POST['itg_log_enable'])){
                update_option('itg_log_enable', 'yes');
            }
            else{
                update_option('itg_log_enable', 'no');
            }
            ?>
            <br><div id="setting-error-settings_updated" class="alert alert-success"> 
                <p><?php echo '<strong>' . __('Settings were saved successfully.', ITG_TEXT_DOMAIN) . '</strong>'; ?></p></div>

            <?php
        endif;
    }

}

AngellEYE_IfThenGive_PayPal_Connect_Setting::init();