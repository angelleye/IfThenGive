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
                    <li class="active"><span href="#gw_account_goals">GiveWhen Goals</span></li>
                    <li><span href="#gw_account_txns">GiveWhen Transactions</span></li>
                    <li><span href="#gw_account_adjust_amount">Adjust Donation Amount</span></li>
                    <li><span href="#gw_account_status">Account Status</span></li>
                </ul>
                <div class="tabs-content">
                    <div class="tab-pane active" id="gw_account_goals">
                        <h4>GiveWhen Goals</h4>
                        <p></p>
                    </div>
                    <div class="tab-pane" id="gw_account_txns">
                        <h4>GiveWhen Transactions</h4>
                        <p></p>
                    </div>
                    <div class="tab-pane" id="gw_account_adjust_amount">
                        <h4>Adjust Donation Amount</h4>
                        <p></p>
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