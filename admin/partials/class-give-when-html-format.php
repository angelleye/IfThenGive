<?php

/**
 * This class defines all code necessary to generate interface
 * @class       AngellEYE_Give_When_interface
 * @version	1.0.0
 * @package	give-when/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_interface { 
    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {        
        add_action('give_when_interface', array(__CLASS__, 'give_when_interface_html'));
        add_action('give_when_shortcode_interface',array(__CLASS__, 'give_when_shortcode_interface_html'));
        add_action('give_when_givers_interface',array(__CLASS__, 'give_when_givers_interface_html'));
        add_action('give_when_do_transactions_interface',array(__CLASS__, 'give_when_do_transactions_interface_html'));
        add_action('give_when_list_transactions_interface',array(__CLASS__, 'give_when_list_transactions_interface_html'));
    }
    
    /**
     * give_when_interface_html function is for
     * html of interface when action is Edit.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_interface_html() {    
        $action_request= isset($_REQUEST['view']) ? $_REQUEST['view'] : '';
        global $post;           
        $trigger_name = !empty(get_post_meta($post->ID,'trigger_name',true)) ? get_post_meta($post->ID,'trigger_name',true) : '';
        $trigger_thing = !empty(get_post_meta($post->ID,'trigger_thing',true)) ? get_post_meta($post->ID,'trigger_thing',true) : '';
        $trigger_desc = !empty(get_post_meta($post->ID,'trigger_desc',true)) ? get_post_meta($post->ID,'trigger_desc',true) : '';
        $image_url    = !empty(get_post_meta($post->ID,'image_url',true)) ? get_post_meta($post->ID,'image_url',true) : '';        
        $gw_amount = !empty(get_post_meta($post->ID,'amount',true)) ? get_post_meta($post->ID,'amount',true) : '';        
        if($gw_amount == 'fixed'){
            $fixed_amount_check = 'checked';
            $fixed_amount_input_class = "";
            $fixed_amount_input_value = !empty(get_post_meta($post->ID,'fixed_amount_input',true)) ? get_post_meta($post->ID,'fixed_amount_input',true) : '';
        }
        else{
            $fixed_amount_check = '';
            $fixed_amount_input_class = "hidden";
            $fixed_amount_input_value = '';
        }
        
        if($gw_amount == 'select'){
            $dynamic_options_check = 'checked';
            $dynamic_options_class = '';
            $dynamic_options_name = !empty(get_post_meta($post->ID,'option_name',true)) ? get_post_meta($post->ID,'option_name',true) : '';
            $dynamic_option_amount = !empty(get_post_meta($post->ID,'option_amount',true)) ? get_post_meta($post->ID,'option_amount',true) : '';
        }else{
            $dynamic_options_check = '';
            $dynamic_options_class = 'hidden';
        }
        
         if($gw_amount == 'manual'){
             $manual_options_check ='checked';
         }
         else{
             $manual_options_check ='';
         }
        
        $screen = get_current_screen();
        if($screen->action=='add'){
             $fixed_amount_check = 'checked';
             $fixed_amount_input_class = "";
        }
?>
        
        <div style="padding-top: 25px"></div>
        <div class="container" style="max-width: 100%">   
            <form>
                <div class="form-group">
                    <label for="triggerName" class="control-label">Goal Name</label>
                    <input type="text" name="trigger_name" value="<?php echo $trigger_name?>" class="form-control" autocomplete="off" id="trigger_name" placeholder="Enter Name Here"/>
                </div>
                <div class="form-group">
                    <label for="triggerName" class="control-label">Thing</label>
                    <input type="text" name="trigger_thing" value="<?php echo $trigger_thing?>" class="form-control" autocomplete="off" id="trigger_name" placeholder="Enter event Here"/>
                </div>
                <div class="form-group">
                    <label for="triggerDesc" class="control-label">Goal Description</label>
                    <textarea name="trigger_desc" class="form-control" autocomplete="off" id="trigger_desc" placeholder="Enter Description Here"><?php echo $trigger_desc;?></textarea>
                </div>
                <div class="form-group">
                    <label for="image_url">Image</label>
                    <input type="text" name="image_url" id="image_url" class="form-control" value="<?php echo $image_url; ?>"><br>
                    <input type="button" name="upload-btn" id="upload-btn" class="btn btn-primary" value="Upload Image">
                </div>                
                <div class="form-group">
                    <input type="radio" name="fixed_radio" id="fixed_radio" value="fixed" <?php echo $fixed_amount_check; ?>><label class="radio-inline" for="fixed_radio"><strong>Fixed</strong></label>
                     &nbsp;
                     <input type="radio" name="fixed_radio" id="option_radio" value="select" <?php echo $dynamic_options_check; ?>><label class="radio-inline" for="option_radio"><strong>Select</strong></label>
                     &nbsp;
                     <input type="radio" name="fixed_radio" id="manual_radio" value="manual" <?php echo $manual_options_check; ?>><label class="radio-inline" for="manual_radio"><strong>Allow User to Manually Add</strong></label>
                </div>                
                
                <div class="form-group <?php echo $fixed_amount_input_class; ?>" id="fixed_amount_input_div">
                    <label for="triggerName" class="control-label">Fixed Amount</label>
                    <input type="text" name="fixed_amount_input" value="<?php echo $fixed_amount_input_value; ?>" class="form-control" autocomplete="off" id="fixed_amount_input" placeholder="Enter Amount"/>
                </div>
                <?php //print_r($dynamic_options_name); ?>
                <div id="dynamic_options" class="<?php echo $dynamic_options_class; ?>">                    
                    <?php 
                    if(!empty($dynamic_options_name)) {
                    $i=0;
                    $total_options = count($dynamic_options_name);
                    ?> <div id="education_fields"> 
                    <?php
                    foreach($dynamic_options_name as $name){ ?>       
                        <div class="form-group removeclass<?php echo ($i+1); ?>">
                        <div class="col-sm-1 nopadding">
                            <label class="control-label">Option </label>
                        </div>
                        <div class="col-sm-3 nopadding">
                            <div class="form-group">
                                <input type="text" class="form-control" id="option_name" name="option_name[]" value="<?php echo $name; ?>" placeholder="Option Name">
                            </div>
                        </div>                
                        <div class="col-sm-3 nopadding">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="option_amount" name="option_amount[]" value="<?php echo $dynamic_option_amount[$i]; ?>" placeholder="Option Amount">
                                    <div class="input-group-btn">
                                        <?php if(($i+1) == $total_options) { ?>
                                            <button class="btn btn-success" type="button" id="add_dynamic_field"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                                        <?php } else { ?>                                            
                                            <button class="btn btn-danger" type="button" id="remove_dynamic_fields" data-fieldid="<?php echo ($i+1); ?>"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span> </button>                                            
                                        <?php } ?>    
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                       </div>
                        
                    <?php $i++; } ?> </div> <?php   
                    }
                    else{
                    ?>
                    <div id="education_fields">
                    <div class="col-sm-1 nopadding">
                        <label class="control-label">Option </label>
                    </div>
                    <div class="col-sm-3 nopadding">
                        <div class="form-group">
                            <input type="text" class="form-control" id="option_name" name="option_name[]" value="" placeholder="Option Name">
                        </div>
                    </div>                
                    <div class="col-sm-3 nopadding">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="option_amount" name="option_amount[]" value="" placeholder="Option Amount">
                                <div class="input-group-btn">
                                    <button class="btn btn-success" type="button" id="add_dynamic_field"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    </div>
                    <?php } ?>                    
                </div>    
            </form>
        </div>
<?php
    }    
    
    /**
     * give_when_shortcode_interface_html function is for
     * html of interface when action is View.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_shortcode_interface_html(){
         global $post, $post_ID;
    ?>        
        <div class="give_when_container">
            <div class="row">
                <div class="col-md-12">
                    <p>You can easily place this button in your pages and posts using this tool....</p>
                    <img src="<?php echo GW_PLUGIN_URL; ?>/admin/images/give_when_tool.png" class="img-responsive" style="margin: 0 auto;"/>
                </div>
            </div>
            <div class="row">
                <div class="text-center">OR</div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <p>You may copy and paste this shortcode into any Page or Post to place the "Goal" where you would like it to be displayed.</p>
                    <div class="zero-clipboard"><span class="btn-clipboard" data-toggle="tooltip" data-placement="left" title="Copy To Clipboard">Copy</span></div>
                    <div class="give_when_highlight">
                        <h4><?php echo '[give_when_goal id=' . $post_ID . ']'; ?></h4>                        
                    </div>
                </div>
            </div>
        </div>        
        <script type="text/javascript">
            jQuery('[data-toggle="tooltip"]').tooltip();

            var clipboard = new Clipboard('.btn-clipboard', {
                target: function() {
                    return document.querySelector('.give_when_highlight');
                }
            });  
            /* Below code will use whenever we want further clipboard work */
            /*clipboard.on('success', function(e) {
                console.log(e);
            });

            clipboard.on('error', function(e) {
                console.log(e);
            });*/
        </script>
    <?php
    }
    
    public static function give_when_givers_interface_html(){
        global $post, $post_ID;
        
        ?>
        <form method="post" id="ks"></form>
        <div class="give_when_container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <span class="text-info">Click <strong>"FUN"</strong> Button to Capture your Transactions.</span><br/>                    
                       <a class="btn btn-primary btn-lg" id="give_when_fun" href="<?php echo site_url(); ?>/wp-admin/post.php?post=<?php echo $post_ID; ?>&action=edit&view=DoTransactions" onclick="return confirm('Ready to process payments based on this goal occurrence?')">Fun</a>
                </div>
            </div>            
            <div class="row">
                <div class="col-md-12">
                                     
                    <?php
                        $table = new AngellEYE_Give_When_Givers_Table();
                        $table->prepare_items();
                        echo '<form method="post" action="">';
                        //echo '<input type="hidden" name="page" value="296">';
                        //$table->search_box('Search', 'givers_search_id');
                        $table->display();
                        echo '</form>';
                    ?>                    
                </div>                
            </div>
        </div>   
        <?php
    }
    
    public static function give_when_do_transactions_interface_html(){        
        global $post, $post_ID;
        $goal_id = $post_ID;       
        $givers = AngellEYE_Give_When_Givers_Table::get_all_givers();        
        $PayPal_config = new Give_When_PayPal_Helper();   
        $paypal_account_id = get_option('give_when_permission_connected_person_payerID');
        $PayPal_config->set_api_subject($paypal_account_id);
        $PayPal = new Angelleye_PayPal($PayPal_config->get_configuration());
        
        
        foreach ($givers as $value) {
            $trigger_name = get_post_meta($post->ID,'trigger_name',true);
            $desc = !empty($trigger_name) ? $trigger_name : '';
            
            $DRTFields = array(
                'referenceid' => $value['BillingAgreement'],
                'paymentaction' => 'Authorization',				                   
            );
            
            $PaymentDetails = array(
                'amt' => $value['amount'],
                //'currencycode' => $value['give_when_gec_currency_code'],
                'desc' => $desc,
                'custom' => 'user_id_'.$value['user_id'].'|post_id_'.$post->ID,
            );
            
            $PayPalRequestData = array(
                'DRTFields' => $DRTFields, 
                'PaymentDetails' => $PaymentDetails,               
            );            
         $PayPalResultDRT = $PayPal->DoReferenceTransaction($PayPalRequestData);
                  
         if($PayPal->APICallSuccessful($PayPalResultDRT['ACK'])){
            $new_post_id = wp_insert_post( array(
                'post_status' => 'publish',
                'post_type' => 'gw_transactions',
                'post_title' => ('UserID:'.$value['user_id'].'|GoalID:'.$goal_id.'|TxnID :'.$PayPalResultDRT['TRANSACTIONID'])
            ) );
            update_post_meta($new_post_id,'give_when_transactions_amount',$value['amount']);
            update_post_meta($new_post_id,'give_when_transactions_wp_user_id',$value['user_id']);
            update_post_meta($new_post_id,'give_when_transactions_wp_goal_id',$goal_id);
            update_post_meta($new_post_id,'give_when_transactions_transaction_id',$PayPalResultDRT['TRANSACTIONID']);
         }
         else{
             // save to error log
         }
        }
        echo '<div class="alert alert-success">
                <p>You have successfully Captured All Transactions.</p>
             </div>';
    }
    
    public static function give_when_list_transactions_interface_html(){
        global $post, $post_ID;
        ?>
        <div class="give_when_container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3 class="text-info">Transactions</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form method="post">
                    <?php                        
                        $table = new AngellEYE_Give_When_Transactions_Table();
                        $table->prepare_items();
                        //$table->search_box('Search', 'givers_transaction_search_id');
                        $table->display();
                    ?>
                    </form>
                </div>                
            </div>
        </div>        
        <?php
    }
}
AngellEYE_Give_When_interface::init();