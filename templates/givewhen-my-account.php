<?php
/**
 * GiveWhen My Account template.
 *
 * This template can be overriden by copying this file to your-theme/GiveWhen/gw-errors-display.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	Givewhen
 * @version     1.0.0
 */
if (!defined('ABSPATH'))
    exit; // Don't allow direct access
if (!is_admin()) {
    ?>

    <?php
    get_header();
    ?>
    <div class="gw_center_container">
        <div class="gwcontainer">
            <div class="gw_heading gw_heading-center">                        
                <h3><?php _e("GiveWhen Account", "givewhen"); ?></h3>
            </div>   
            <div id="gw-tabs" class="tabs tabs-vertical border">                    
                <ul class="tabs-navigation">
                    <li class="active"><span href="#gw_account_txns"><?php _e('GiveWhen Transactions','givewhen'); ?></span></li>
                    <li><span href="#gw_account_goals"><?php _e('GiveWhen Goals','givewhen'); ?></span></li>                    
                    <li><span href="#gw_account_status"><?php _e('Account Status','givewhen'); ?></span></li>
                </ul>
                <div class="tabs-content">
                    <div class="tab-pane active" id="gw_account_txns">                        
                        <?php echo do_shortcode('[givewhen_my_transaction]'); ?>
                    </div>
                    <div class="tab-pane " id="gw_account_goals">
                        <?php echo do_shortcode('[givewhen_my_goals]'); ?>
                    </div>
                    <div class="tab-pane" id="gw_account_status">
                        <h4>Account Status</h4>
                        <p></p>
                    </div>
                </div>
            </div>            
        </div>
    </div>
    <?php
    get_footer();
}
?>