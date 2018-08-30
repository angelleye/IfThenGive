<?php
/**
 * IfThenGive My Account template.
 *
 * This template can be overriden by copying this file to your-theme/ifthengive/template-ifthengive-my-account.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	IfThenGive
 * @version     0.1.0
 */
if (!defined('ABSPATH'))
    exit; // Don't allow direct access
if (!is_admin()) {
    $is_endpoint = false;
    global $wp_query;
    if(isset($wp_query->query_vars['itgmyaccount'])){
        $is_endpoint = true;
    }
    ?>

    <?php
    $add_class = '';
    if($is_endpoint) { get_header(); $add_class='itg_endpoint_class'; }
    ?>
    <div class="itg_center_container <?php echo $add_class; ?>">
        <div class="itgcontainer">
            <div class="itg_heading itg_heading-center" style="padding-top: 10px;padding-bottom: 10px">                        
                <h3><?php esc_html_e("Account", 'ifthengive'); ?></h3>
            </div>   
            <div id="itg-tabs" class="tabs border">                    
                <ul class="tabs-navigation">
                    <li class="active"><span href="#itg_account_txns"><?php esc_html_e('Transactions','ifthengive'); ?></span></li>
                    <li><span href="#itg_account_goals"><?php esc_html_e('Goals','ifthengive'); ?></span></li>
                    <li><span href="#itg_account_info"><?php esc_html_e('Account','ifthengive'); ?></span></li>
                </ul>
                <div class="tabs-content">
                    <div class="tab-pane active" id="itg_account_txns">                        
                        <?php echo do_shortcode('[ifthengive_transactions]'); ?>
                    </div>
                    <div class="tab-pane " id="itg_account_goals">
                        <?php echo do_shortcode('[ifthengive_goals]'); ?>
                    </div> 
                    <div class="tab-pane " id="itg_account_info">
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