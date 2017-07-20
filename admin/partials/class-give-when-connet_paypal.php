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
        
        add_action( 'wp_ajax_sandbox_enabled', array(__CLASS__,'sandbox_enabled'));
        add_action("wp_ajax_nopriv_sandbox_enabled",  array(__CLASS__,'sandbox_enabled'));
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
            'desc' => sprintf(__('Save Logs for GiveWhen.', 'angelleye_give_when'))
        );        
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options_setting');
        return $fields;
    }

    public static function give_when_connect_to_paypal_create_setting() {        
        
        $success_notice = get_option('give_when_permission_connect_to_paypal_success_notice');
        if($success_notice){            
            echo '<div class="alert alert-success">';
                echo "<strong>".__($success_notice,'angelleye_give_when')."</strong>";
            echo '</div>';
            delete_option('give_when_permission_connect_to_paypal_success_notice');
        }
        
        $failed_notice = get_option('give_when_permission_connect_to_paypal_failed_notice');
        if($failed_notice){              
            echo '<div class="alert alert-danger">';
                echo "<strong>".__($failed_notice,'angelleye_give_when') ."</strong>";
            echo '</div>';
            delete_option('give_when_permission_connect_to_paypal_failed_notice');
        }
        $conncet_to_paypal_flag = get_option('give_when_permission_connected_to_paypal');
        $genral_setting_fields = self::give_when_connect_to_paypal_setting_fields();
        $Html_output = new AngellEYE_Give_When_Html_output();
        if($conncet_to_paypal_flag == 'Yes'){
            $label = 'You are Connected With PayPal';
            $labelClass = 'label-success';
        }
        else{
            $label = 'You are not Connected With PayPal';
            $labelClass = 'label-danger';
        }
        /*
        ?>  
            <div class="wrap">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h1 class="text-info">&nbsp;<?php echo __('PayPal User Infromation','angelleye_give_when');?></h1>
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
                                                    <?php echo __('PayPal Account ID','angelleye_give_when');?>
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
                                                    <?php echo __('Name','angelleye_give_when');?>                                                                                                    
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
                                                    <?php echo __('Email','angelleye_give_when');?>
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
                            <div class="alert alert-info"><span class="circle_green"></span><span><?php echo __('You are already connected to PayPal...!!','angelleye_give_when'); ?></span></div>
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
        else {*/
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
                                <div class="pull-right"><span class="label <?php echo $labelClass; ?>"><?php _e($label,'angelleye_give_when'); ?></span></div>
                          </div>
                          <div class="panel-body">
                            <div class="row">
                              <div class="col-lg-12 col-md-12 col-sm-12">
                                <p>Gives your buyers a simplified checkout experience on multiple devices that keeps them local to your website throughout the payment authorization process <a href="https://www.paypal.com/uk/webapps/mpp/express-checkout" target="_blank">(Learn more)</a></p>
                              </div>
                            </div>
                            <div class="row">                             
                              <div class="col-lg-3 col-md-3 col-sm-3">
                                <p><a id="angelleye_connect_to_paypal" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Connect with PayPal">Connect with PayPal</a></p>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>                    
                </div>
                <div id="overlay" style=" background: #f6f6f6;opacity: 0.7;width: 100%;float: left;height: 100%;position: fixed;top: 0;z-index: 1031;text-align: center; display: none;">
                    <div style="display: table; width:100%; height: 100%;">                
                        <div style="display: table-cell;vertical-align: middle;"><img src="<?php echo GW_PLUGIN_URL; ?>/admin/images/loading.gif"  style=" position: relative;top: 50%;"/>
                        <h2><?php echo __('Please Don\'t Go back , We are redirecting you to PayPal.','angelleye_give_when'); ?></h2></div>
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
        $sanbox_enable = get_option('sandbox_enable_give_when');
        if($sanbox_enable === 'yes'){
            $sandbox = 'true';
        }else{
            $sandbox = 'false';
        }
        $url = 'http://angelleye.project-demo.info/paypal/';
        $return_url = site_url('?action=permission_callback');
        $postData = "sandbox={$sandbox}&api=connect_to_paypal&return_url={$return_url}";
        $ConnectPayPalJson = self::curl_request($url,$postData);
        $ConnectPayPalArray = json_decode($ConnectPayPalJson, true);
        if($ConnectPayPalArray['ACK'] == 'success'){
            echo json_encode(array('Ack' => 'success','action_url' => $ConnectPayPalArray['action_url']));
        }
        else{
            echo json_encode(array('Ack' => 'failed','errorData'=>$ConnectPayPalArray));
        }
        exit;
    }
    
    
    public function sandbox_enabled(){
        if(isset($_POST['sandbox'])){
            $sandbox = $_POST['sandbox'];
            if($sandbox=='true'){
                update_option('sandbox_enable_give_when','yes');
            }
            else{
                update_option('sandbox_enable_give_when','no');
            }
            echo json_encode(array('Ack' => 'success'));
        }
        exit;
    }
    
    public static function curl_request($url,$postData){        
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
            ?>
        <br><div id="setting-error-settings_updated" class="alert alert-success"> 
                <p><?php echo '<strong>' . __('Settings were saved successfully.', 'angelleye_give_when') . '</strong>'; ?></p></div>

            <?php
        endif;
    }
}

AngellEYE_Give_When_PayPal_Connect_Setting::init();
