<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 *
 * @package    Givewhen
 * @subpackage Givewhen/public/partials
 */
class AngellEYE_Give_When_Public_Display {

    public static function init() {
        add_shortcode('give_when', array(__CLASS__, 'give_when_create_shortcode'));
        add_action( 'wp_enqueue_scripts', array(__CLASS__,'give_when_detect_shortcode'));
        add_action( 'wp_ajax_start_express_checkout', array(__CLASS__,'start_express_checkout'));
        add_action("wp_ajax_nopriv_start_express_checkout",  array(__CLASS__,'start_express_checkout'));
    }

    public static function give_when_detect_shortcode()
    {
        global $post;
        $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches )
            && in_array( 'give_when', $matches[2] ) )
        {            
            wp_enqueue_style( 'givewhen-one', GW_PLUGIN_URL . 'includes/css/bootstrap/css/bootstrap.css', array(), '1.0.0','all' );
        }
    }
    
    /**
     * give_when_create_shortcode function is for generate
     * @since 1.0.0
     * @access public
     */
    public static function give_when_create_shortcode($atts, $content = null) {

        global $post, $post_ID;        
        extract(shortcode_atts(array(
                    'id' => ''), $atts));
        $html = '';
        
        if( !empty($id) ) {
            $post = get_post($id);
            if(!empty($post->post_type) && $post->post_type == 'give_when' && $post->post_status == 'publish') {
        ?>
                <div id="overlay" style=" background: #f6f6f6;opacity: 0.7;width: 100%;float: left;height: 100%;position: fixed;top: 0;left:0;right:0;z-index: 1031;text-align: center; display: none;">
                    <div style="display: table; width:100%; height: 100%;">
                        <div style="display: table-cell;vertical-align: middle;"><img src="<?php echo GW_PLUGIN_URL; ?>admin/images/loading.gif"  style=" position: relative;top: 50%; height: 100px"/>
                        <h1>Please dont't go back , We are redirecting you to PayPal</h1></div>
                    </div>            
                </div>
                <div class="give_when_container">
                    <div class="row">
                        <div class="alert alert-warning" id="connect_paypal_error_public" style="display: none">
                                <span id="connect_paypal_error_p"></span>
                        </div>                        
                        <div class="col-md-12"><h1><?php echo get_post_meta( $post->ID, 'trigger_name', true ); ?></h1></div>
                        <div class="col-md-12">
                            <img src="<?php echo get_post_meta( $post->ID, 'image_url', true ) ?>">
                            <br><br>
                            <p> <?php echo get_post_meta( $post->ID, 'trigger_desc', true ); ?></p>
                            <?php echo $post->post_content; ?>
                        </div>
                        <div class="col-md-12">
                            <?php 
                                $amount = get_post_meta($post->ID,'amount',true);
                                if($amount == 'fixed'){
                                    $fixed_amount = get_post_meta($post->ID,'fixed_amount_input',true);
                                    ?>
                                <p class="lead">I will Give : $ <span id="give_when_fixed_price_span"><?php echo $fixed_amount; ?></span> When <?php echo get_post_meta( $post->ID, 'trigger_name', true ); ?></p>
                                <?php    
                                } else{
                                    $option_name = get_post_meta($post->ID,'option_name',true);
                                    $option_amount = get_post_meta($post->ID,'option_amount',true);
                                    $i=0;
                            ?>
                            <p class="lead">I will Give : $ <span id="give_when_fixed_price_span_select"><?php echo $option_amount[0]; ?></span> When <?php echo get_post_meta( $post->ID, 'trigger_name', true ); ?></p>
                            <div class="form-group">
                                <select class="form-control" name="give_when_option_amount" id="give_when_option_amount">
                                <?php
                                    foreach ($option_name as $name) {
                                        echo '<option value="'.$option_amount[$i].'">'.$name.$option_amount[$i].'</option>';
                                        $i++;
                                    }
                                ?>
                                </select>
                            </div>
                            <?php } ?>
                        </div>                       
                    </div>
<!--                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading"> Sign up for <?php echo get_post_meta( $post->ID, 'trigger_name', true ); ?></div>
                                <div class="panel-body">
                                    <form>
                                        <div class="form-group">
                                          <label for="name">Name</label>
                                          <input type="text" class="form-control" id="give_when_name">
                                        </div>
                                        <div class="form-group">
                                          <label for="email">Email address:</label>
                                          <input type="email" class="form-control" id="give_when_email">
                                        </div>
                                        <div class="checkbox">
                                          <label><input type="checkbox"> I accept billing agreement</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Sign Up and Checkout With PayPal</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>-->
                    <div class="row">
                         <div class="col-md-12">
                             <img src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/gold-rect-paypalcheckout-44px.png" alt="PayPal Checkout" style="cursor: pointer" id="give_when_angelleye_checkout" data-postid="<?php echo $id; ?>">
                        </div>
                    </div>                    
                </div>
            <?php
            }
        }
    }
    
    public function start_express_checkout(){
        $post_id = $_POST['post_id'];
        $amount = $_POST['amount'];
        $post = get_post($post_id);
        $trigger_name = get_post_meta( $post->ID, 'trigger_name', true );
        
        $paypal_account_id = get_option('give_when_permission_connected_person_payerID');
        $PayPal_config = new Give_When_PayPal_Helper();
        $PayPal_config->set_api_subject($paypal_account_id);
        
        $PayPal = new Angelleye_PayPal($PayPal_config->get_configuration());
        $SECFields = array(
                'maxamt' => '1000',
                'returnurl' => site_url('?action=ec_return'),
                'cancelurl' => site_url('?action=ec_cancel'),
                'hdrimg' => 'https://www.angelleye.com/images/angelleye-paypal-header-750x90.jpg',
                'logoimg' => 'https://www.angelleye.com/images/angelleye-logo-190x60.jpg',
                'brandname' => 'Angell EYE',
                'customerservicenumber' => '816-555-5555',
        );
        $Payments = array();
        $Payment = array(
            'amt' => 0,
            'PAYMENTACTION' => 'AUTHORIZATION',
            'custom' => 'amount_'.$amount.'|post_id_'.$post_id
        );
        array_push($Payments, $Payment);
        
        $BillingAgreements = array();
        $Item = array(
                'l_billingtype' => 'MerchantInitiatedBilling',
                'l_billingagreementdescription' => $trigger_name,
                'l_paymenttype' => '',
                'l_billingagreementcustom' => 'give_when_'.$post_id
        );
        array_push($BillingAgreements, $Item);

        $PayPalRequestData = array(
            'SECFields' => $SECFields, 
            'Payments' => $Payments,
            'BillingAgreements' => $BillingAgreements,
        );
        $PayPalResult = $PayPal->SetExpressCheckout($PayPalRequestData);
        if($PayPal->APICallSuccessful($PayPalResult['ACK']))
        {            
            echo json_encode(array('Ack'=>'Success','RedirectURL'=>$PayPalResult['REDIRECTURL']));
        }
        else
        {
            echo json_encode(array('Ack'=>'Failure','ErrorCode'=>$PayPalResult['L_ERRORCODE0'],'ErrorShort'=>$PayPalResult['L_SHORTMESSAGE0'],'ErrorLong'=>$PayPalResult['L_LONGMESSAGE0']));            
        }
        exit;
    }
    
}

AngellEYE_Give_When_Public_Display::init();
