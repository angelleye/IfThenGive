<?php
/**
 * IfThenGive My Account template.
 *
 * This template can be overriden by copying this file to your-theme/GiveWhen/template-ifthengive-account-info.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	Givewhen
 * @version     1.0.0
 */

if (!defined('ABSPATH'))
    exit; // Don't allow direct access

if (!is_admin()) {    
    $userID = get_current_user_id();
    if(is_int($userID) && $userID > 0){
        $BAID = get_user_meta($userID,'itg_gec_billing_agreement_id',true);
        $paypal_email = get_user_meta($userID,'itg_gec_email',true);
        $paypal_payer_id = get_user_meta($userID,'itg_gec_payer_id',true);        
?>
<div class="gw_hr-title gw_hr-long gw_center"><abbr><?php _e('Account Info', ITG_TEXT_DOMAIN) ?></abbr></div>
<div class="gwcontainer">
    <div id="canceel_baid_overlay" style=" background: #d9d9da;opacity: 0.9;width: 100%;float: left;height: 100%;position: fixed;top: 0;left:0;right:0;z-index: 1031;text-align: center; display: none;">
        <div class="gw_loader"></div>
        <h1 style="font-weight: 600;">Processing...</h1>
    </div>
    <ul class="gw-list-group">
        <li class="gw-list-group-item">
            <span class="gw_span gw_span_1"><?php _e('Billing Agreement ID : ',ITG_TEXT_DOMAIN);?></span>
            <span class="gw_span gw_span_2"><?php ($BAID!=='') ? _e($BAID,ITG_TEXT_DOMAIN) : _e('-',ITG_TEXT_DOMAIN);?></span>
        </li>
        <li class="gw-list-group-item">
            <span class="gw_span gw_span_1"><?php _e('PayPal Email ID : ',ITG_TEXT_DOMAIN);?></span>
            <span class="gw_span gw_span_2"><?php  ($paypal_email !== '') ? _e($paypal_email,ITG_TEXT_DOMAIN) : _e('-',ITG_TEXT_DOMAIN);?></span>
        </li>        
        <li class="gw-list-group-item">
            <span class="gw_span gw_span_1"><?php _e('PayPal Payer ID : ',ITG_TEXT_DOMAIN);?></span>
            <span class="gw_span gw_span_2"><?php ($paypal_payer_id !=='') ? _e($paypal_payer_id,ITG_TEXT_DOMAIN) : _e('-',ITG_TEXT_DOMAIN); ?></span>
        </li>
        <?php
        if($BAID!=='') {
        ?>
        <li class="gw-list-group-item">
            <button type="button" class="gw_btn gw_btn-primary" id="gw_account_cancel_baid" data-baid="<?php echo $BAID; ?>" data-userid="<?php echo $userID; ?>"><?php _e('Cancel Billing Agreement',ITG_TEXT_DOMAIN); ?></button>
        </li>
        <?php } ?>
    </ul>
    <div class="gw_alert gw_alert-warning" id="cancel_ba_error_public" style="display: none;text-align: left;">
        <div id="gw_cancel_ba_msg"></div>
    </div>         
</div>
<?php    
    }
    else{
        echo "Please login to site.";
    }
}