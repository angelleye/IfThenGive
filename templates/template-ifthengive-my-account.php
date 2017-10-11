<?php
/**
 * IfThenGive My Account template.
 *
 * This template can be overriden by copying this file to your-theme/GiveWhen/template-ifthengive-my-account.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	Givewhen
 * @version     1.0.0
 */
if (!defined('ABSPATH'))
    exit; // Don't allow direct access
if (!is_admin()) {
    $is_endpoint = false;
    global $wp_query;
    if(isset($wp_query->query_vars['gwmyaccount'])){
        $is_endpoint = true;
    }
    ?>

    <?php
    if($is_endpoint) { get_header(); }
    ?>
    <div class="gw_center_container">
        <div class="gwcontainer">
            <div class="gw_heading gw_heading-center">                        
                <h3><?php _e("Account", ITG_TEXT_DOMAIN); ?></h3>
            </div>   
            <div id="gw-tabs" class="tabs border">                    
                <ul class="tabs-navigation">
                    <li class="active"><span href="#gw_account_txns"><?php _e('Transactions',ITG_TEXT_DOMAIN); ?></span></li>
                    <li><span href="#gw_account_goals"><?php _e('Goals',ITG_TEXT_DOMAIN); ?></span></li>
                    <li><span href="#gw_account_info"><?php _e('Account',ITG_TEXT_DOMAIN); ?></span></li>
                </ul>
                <div class="tabs-content">
                    <div class="tab-pane active" id="gw_account_txns">                        
                        <?php echo do_shortcode('[ifthengive_transactions]'); ?>
                    </div>
                    <div class="tab-pane " id="gw_account_goals">
                        <?php echo do_shortcode('[ifthengive_goals]'); ?>
                    </div> 
                    <div class="tab-pane " id="gw_account_info">
                        <?php echo do_shortcode('[ifthengive_account_info]'); ?>
                    </div> 
                </div>
            </div>            
        </div>
    </div>
    <?php
    if($is_endpoint) {  get_footer(); } 
}
?>