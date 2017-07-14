<?php

/**
 * This class defines all code necessary to Companies Setting from admin side
 * @class       AngellEYE_Give_When_General_Setting
 * @version	1.0.0
 * @package     Givewhen
 * @subpackage  Givewhen/admin/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_General_Setting {

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
        add_action('give_when_general_create_setting', array(__CLASS__, 'give_when_general_create_setting'));
        add_action('give_when_general_setting_save_field', array(__CLASS__, 'give_when_general_setting_save_field'));
        //add_action('give_when_general_setting', array(__CLASS__, 'give_when_general_setting'));
        add_action( 'wp_ajax_sandbox_enabled', array(__CLASS__,'sandbox_enabled'));
        add_action("wp_ajax_nopriv_sandbox_enabled",  array(__CLASS__,'sandbox_enabled'));
    }

    public static function give_when_general_setting_fields() {
        global $wpdb;
        $fields[] = array(
            'title' => __('Enable Sandbox / Testing', 'angelleye_give_when'),
            'id' => 'sandbox_enable_give_when',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'angelleye_give_when'),
            'default' => 'no',
            'labelClass' => 'switch',
            'desc' => sprintf(__('<div class="slider round"></div>', 'angelleye_give_when'))
        );
        $fields[] =  array(
            'id' => 'give_when_sandbox_api_username',
            'title' => __('Sandbox API Username', 'angelleye_give_when'),
            'type' => 'text',
            'class' => 'form-control sandbox'
        );
        $fields[] = array(
            'id' => 'give_when_sandbox_api_password',
            'title' => __('Sandbox API Password', 'angelleye_give_when'),
            'type' => 'password',            
            'class' => 'form-control sandbox'
        );
        $fields[] = array(
            'id' => 'give_when_sandbox_api_signature',
            'title' => __('Sandbox API Signature', 'angelleye_give_when'),
            'type' => 'password',
            'class' => 'form-control sandbox'            
        );
        $fields[] = array(
            'id' => 'give_when_api_username',
            'title' => __('Live API User Name', 'angelleye_give_when'),
            'type' => 'text',
            'description' => __('Get your live account API credentials from your PayPal account profile under the API Access section <br />or by using <a target="_blank" href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_login-api-run">this tool</a>.', 'angelleye_give_when'),
            'default' => '',
            'class' => 'form-control live'
        );
        $fields[] = array(
            'id' => 'give_when_api_password',
            'title' => __('Live API Password', 'angelleye_give_when'),
            'type' => 'password',
            'default' => '',
            'class' => 'form-control live'
        );
        $fields[] = array(
            'id' => 'give_when_api_signature',
            'title' => __('Live API Signature', 'angelleye_give_when'),
            'type' => 'password',
            'default' => '',
            'class' => 'form-control live'            
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

    public static function give_when_general_create_setting() {
        $genral_setting_fields = self::give_when_general_setting_fields();
        $Html_output = new AngellEYE_Give_When_Html_output();
        ?>
        <div class="wrap">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <br><br><br><br><br><br>
                        <div class="panel panel-primary">
                            <div class="panel-heading"><strong><?php echo __('PayPal Setup Instructions','angelleye_give_when'); ?></strong></div>
                            <div class="panel-body">
                                <ul>
                                    <li class="gw_sandboxList"><?php echo __('To run test transactions you will need to','angelleye_give_when'); ?><a href="https://www.angelleye.com/create-paypal-sandbox-account/" target="_blank"> <?php echo __('create at least one buyer and one seller sandbox account at the PayPal Developer site','angelleye_give_when'); ?></a>.</li>
                                    <li class="gw_sandboxList"><?php printf( __('When you have a sandbox seller account ready to configure for testing with your site,','angelleye_give_when'),''); ?> <a href="https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&amp;generic-flow=true" target="_blank"><?php echo __('use this link','angelleye_give_when'); ?></a> <?php echo __('and login with that seller account to quickly obtain API credentials for the account.','angelleye_give_when'); ?></li>
                                    <li class="gw_liveList"><?php echo __('You may','angelleye_give_when'); ?><a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&amp;generic-flow=true" target="_blank"><?php echo __('use this link','angelleye_give_when'); ?></a> <?php echo __('to quickly obtain your PayPal account API credentials.','angelleye_give_when'); ?></li>
                                    <li><?php echo __('Then copy and paste the API username, password, and signature into the plugin settings on the left and save your settings.','angelleye_give_when'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php    
    }         

    /**
     * give_when_general_setting_save_field function used for save general setting field value
     * @since 1.0.0
     * @access public static
     * 
     */
    public static function give_when_general_setting_save_field() {
        $givewhen_setting_fields = self::give_when_general_setting_fields();
        $Html_output = new AngellEYE_Give_When_Html_output();
        $Html_output->save_fields($givewhen_setting_fields);
        
        $PayPal_config = new Give_When_PayPal_Helper();
        $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());        
        
        if (isset($_POST['give_when_intigration'])):
            ?>
        <br><div id="setting-error-settings_updated" class="alert alert-success"> 
                <p><?php echo '<strong>' . __('Settings were saved successfully.', 'angelleye_give_when') . '</strong>'; ?></p></div>

            <?php
        endif;
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
    }
}

AngellEYE_Give_When_General_Setting::init();
