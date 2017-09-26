<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 * @class      AngellEYE_Give_When_Cancel_Billing_Agreement
 * @package    Givewhen
 * @subpackage Givewhen/admin/partials
 * @category    Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_Cancel_Billing_Agreement {
    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {
        add_action('delete_user', array(__CLASS__, 'Cancel_Billing_Agreement_delete_giver'));
        add_action('delete_user_form', array(__CLASS__, 'Cancel_Billing_Agreement_delete_giver_message'));
    }
    public static function Cancel_Billing_Agreement_delete_giver( $user_id ) {        
        $billing_agreement_id = get_user_meta( $user_id, 'give_when_gec_billing_agreement_id', true );
        $PayPal_config = new Give_When_PayPal_Helper();        
        $PayPal_config->set_api_cedentials();   
        $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
        /*
         *   By default Angell EYE PayPal PHP Library has ButtonSource is "AngellEYE_PHPClass".
         *   We are overwirting that variable with "AngellEYE_GiveWhen" value.
         *   It also reflactes in NVPCredentials string so we are also replcing it.
         */
        $PayPal->APIButtonSource = GW_BUTTON_SOURCE;
        $PayPal->NVPCredentials = str_replace('AngellEYE_PHPClass',GW_BUTTON_SOURCE,$PayPal->NVPCredentials);        

        $BAUpdateFields = array(
            'REFERENCEID' => $billing_agreement_id,           
            'BILLINGAGREEMENTSTATUS' => 'Canceled',
            'BILLINGAGREEMENTDESCRIPTION' => 'Givewhen Giver Role deleted.'
        );        
        $PayPalRequestData = array('BAUFields' => $BAUpdateFields);        
        $PayPalResult = $PayPal->BillAgreementUpdate($PayPalRequestData);        
        //print_r($PayPalResult);
    }
    public static function Cancel_Billing_Agreement_delete_giver_message($users){
        if(isset($_REQUEST['user'])){
            $user_meta=get_userdata($_REQUEST['user']); 
            $user_roles=$user_meta->roles; 
            if (in_array("giver", $user_roles)){
                echo '<b>'.__('Deleting this Giver will also cancel their PayPal billing agreement.').'</b>';
            }
        }
        if(isset($_REQUEST['users']) && !empty($_REQUEST['users'])){
            foreach ($_REQUEST['users'] as $user_id) {
                $user_meta=get_userdata($user_id); 
                $user_roles=$user_meta->roles; 
            }
            if (in_array("giver", $user_roles)){
                echo '<b>'.__('Deleting this Givers will also cancel their PayPal billing agreement.').'</b>';
            }
        }
        
    }

}

AngellEYE_Give_When_Cancel_Billing_Agreement::init();
