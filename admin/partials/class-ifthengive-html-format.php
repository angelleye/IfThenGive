<?php
use Dompdf\Dompdf;
use Dompdf\Options;
/**
 * This class defines all code necessary to generate interface
 * @class       AngellEYE_IfThenGive_interface
 * @version	1.0.0
 * @package	ifthengive/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_interface {
    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {
        add_action('ifthengive_interface', array(__CLASS__, 'ifthengive_interface_html'));
        add_action('ifthengive_shortcode_interface', array(__CLASS__, 'ifthengive_shortcode_interface_html'));
        add_action('ifthengive_givers_interface', array(__CLASS__, 'ifthengive_givers_interface_html'));
        add_action('ifthengive_do_transactions_interface', array(__CLASS__, 'ifthengive_do_transactions_interface_html'));
        add_action('ifthengive_list_transactions_interface', array(__CLASS__, 'ifthengive_list_transactions_interface_html'));
        add_action('ifthengive_get_transaction_detail', array(__CLASS__, 'ifthengive_get_transaction_detail_html'));
        add_action('ifthengive_retry_failed_transactions_interface', array(__CLASS__, 'ifthengive_retry_failed_transactions_interface_html'));
        add_action('ifthengive_disconnect_interface',array(__CLASS__,'ifthengive_disconnect_interface_html'));
        add_action('ifthengive_get_users_transactions_interface',array(__CLASS__,'ifthengive_get_users_transactions_interface_html'));
        add_action('admin_head', array(__CLASS__, 'ifthengive_hide_publish_button_until'));
        add_action('wp_ajax_cancel_billing_agreement_giver', array(__CLASS__, 'cancel_billing_agreement_giver'));
        add_action("wp_ajax_nopriv_cancel_billing_agreement_giver", array(__CLASS__, 'cancel_billing_agreement_giver'));
        
        add_action('wp_ajax_check_goal_is_in_process', array(__CLASS__, 'check_goal_is_in_process'));
        add_action("wp_ajax_nopriv_check_goal_is_in_process", array(__CLASS__, 'check_goal_is_in_process'));
        
        add_action('wp_ajax_delete_giver_from_goal', array(__CLASS__, 'delete_giver_from_goal'));
        add_action("wp_ajax_nopriv_delete_giver_from_goal", array(__CLASS__, 'delete_giver_from_goal'));
        
    }

    /**
     * ifthengive_interface_html function is for
     * HTML interface when action is Edit.
     * This method also allow you to preview the Goal details
     * @since 1.0.0
     * @access public
     */
    public static function ifthengive_interface_html() {
        $connect_to_sandbox_paypal_flag = get_option('itg_sb_connected_to_paypal');
        $connect_to_live_paypal_flag = get_option('itg_live_connected_to_paypal');
        if ($connect_to_sandbox_paypal_flag != 'Yes' && $connect_to_live_paypal_flag != 'Yes') {
            ?>
            <div style="padding-top: 25px"></div>
            <div class="container" style="max-width: 100%">
                <div class="bs-callout bs-callout-warning">
                    <h4><?php echo __('You are not Connected with PayPal.', ITG_TEXT_DOMAIN); ?></h4>
                    <a href="<?php echo site_url(); ?>/wp-admin/options-general.php?page=ifthengive_option"><?php echo __('Click Here', ITG_TEXT_DOMAIN); ?></a><?php echo __(' for Setting page to Connect With PayPal.', ITG_TEXT_DOMAIN); ?>
                </div>               
            </div>
            <?php
        } else {
            $action_request = isset($_REQUEST['view']) ? $_REQUEST['view'] : '';
            global $post;            
            $trigger_name = get_post_meta($post->ID, 'trigger_name', true);
            $trigger_thing = get_post_meta($post->ID, 'trigger_thing', true);
            $trigger_desc = get_post_meta($post->ID, 'trigger_desc', true);
            $image_url = get_post_meta($post->ID, 'image_url', true);
            $itg_amount = get_post_meta($post->ID, 'amount', true);
            if ($itg_amount == 'fixed') {
                $fixed_amount_check = 'checked';
                $fixed_amount_input_class = "";
                $fixed_amount_input_value = get_post_meta($post->ID, 'fixed_amount_input', true);
            } else {
                $fixed_amount_check = '';
                $fixed_amount_input_class = "hidden";
                $fixed_amount_input_value = '';
            }

            if ($itg_amount == 'select') {
                $dynamic_options_check = 'checked';
                $dynamic_options_class = '';
                $dynamic_options_name = get_post_meta($post->ID, 'option_name', true);
                $dynamic_option_amount = get_post_meta($post->ID, 'option_amount', true);
            } else {
                $dynamic_options_check = '';
                $dynamic_options_class = 'hidden';
            }
            
            if ($itg_amount == 'manual') {
                $manual_options_check = 'checked';
                $manual_amount_class = "";
                $manual_amount_input_value = get_post_meta($post->ID, 'manual_amount_input', true);
            } else {
                $manual_options_check = '';
                $manual_amount_class = "hidden";
                $manual_amount_input_value = '';
            }            

            $screen = get_current_screen();
            if ($screen->action == 'add') {
                $fixed_amount_check = 'checked';
                $fixed_amount_input_class = "";
            }
            ?>

            <div style="padding-top: 25px"></div>
            <div class="container" style="max-width: 100%">   
                <form>                    
                    <div class="form-group">
                        <label for="triggerName" class="control-label"><?php echo __('Goal Label', ITG_TEXT_DOMAIN); ?></label>
                        <input type="text" name="trigger_thing" value="<?php echo esc_attr( $trigger_thing , ITG_TEXT_DOMAIN); ?>" class="form-control" autocomplete="off" id="trigger_thing" placeholder="Enter event Here"/>
                    </div>
                    <div class="form-group">
                        <label for="triggerDesc" class="control-label"><?php echo __('Goal Description', ITG_TEXT_DOMAIN); ?></label>
                        <textarea name="trigger_desc" class="form-control" autocomplete="off" id="trigger_desc" placeholder="Enter Description Here"><?php echo esc_attr( $trigger_desc , ITG_TEXT_DOMAIN); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image_url"><?php echo __('Image', ITG_TEXT_DOMAIN); ?></label>
                        <input type="text" name="image_url" id="image_url" class="form-control" value="<?php echo esc_attr( $image_url , ITG_TEXT_DOMAIN); ?>"><br>
                        <input type="button" name="upload-btn" id="upload-btn" class="btn btn-primary" value="<?php echo esc_attr( "Add Image" , ITG_TEXT_DOMAIN); ?>">
                    </div>                
                    <div class="form-group">
                        <input type="radio" name="fixed_radio" id="fixed_radio" value="fixed" <?php echo $fixed_amount_check; ?>><label class="radio-inline" for="fixed_radio"><strong><?php echo __('Fixed', ITG_TEXT_DOMAIN); ?></strong></label>
                        &nbsp;
                        <input type="radio" name="fixed_radio" id="option_radio" value="select" <?php echo $dynamic_options_check; ?>><label class="radio-inline" for="option_radio"><strong><?php echo __('Select', ITG_TEXT_DOMAIN); ?></strong></label>
                        &nbsp;
                        <input type="radio" name="fixed_radio" id="manual_radio" value="manual" <?php echo $manual_options_check; ?>><label class="radio-inline" for="manual_radio"><strong><?php echo __('Allow User to Manually Add', ITG_TEXT_DOMAIN); ?></strong></label>
                    </div>                

                    <div class="form-group <?php echo $fixed_amount_input_class; ?>" id="fixed_amount_input_div">
                        <label for="triggerName" class="control-label"><?php echo __('Fixed Amount', ITG_TEXT_DOMAIN); ?></label>
                        <input type="text" name="fixed_amount_input" value="<?php echo esc_attr($fixed_amount_input_value, ITG_TEXT_DOMAIN); ?>" class="form-control" autocomplete="off" id="fixed_amount_input" placeholder="Enter Amount"/>
                    </div>
                    <div class="form-group <?php echo $manual_amount_class; ?>" id="manual_amount_input_div">
                        <label for="manualamount" class="control-label"><?php echo __('Set Default Amount', ITG_TEXT_DOMAIN); ?></label>
                        <input type="text" name="manual_amount_input" value="<?php echo esc_attr($manual_amount_input_value, ITG_TEXT_DOMAIN); ?>" class="form-control" autocomplete="off" id="manual_amount_input" placeholder="Enter Amount"/>
                    </div>
                    <div id="dynamic_options" class="<?php echo $dynamic_options_class; ?>">                        
            <?php
            if (!empty($dynamic_options_name)) {
                $i = 0;
                $total_options = count($dynamic_options_name);
                ?> <div id="education_fields"> 
                            <?php foreach ($dynamic_options_name as $name) { ?>       
                                    <div class="form-group removeclass<?php echo ($i + 1); ?>">
                                        <div class="col-sm-1 nopadding">
                                            <label class="control-label"><?php echo __('Option', ITG_TEXT_DOMAIN); ?> </label>
                                        </div>
                                        <div class="col-sm-3 nopadding">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="option_name<?php echo ($i + 1); ?>" name="option_name[]" value="<?php echo esc_attr($name,ITG_TEXT_DOMAIN); ?>" placeholder="Option Name">
                                            </div>
                                        </div>                
                                        <div class="col-sm-3 nopadding">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="option_amount<?php echo ($i + 1); ?>" name="option_amount[]" value="<?php echo esc_attr($dynamic_option_amount[$i],ITG_TEXT_DOMAIN); ?>" placeholder="Option Amount">
                                                    <div class="input-group-btn">
                    <?php if (($i + 1) == $total_options) { ?>
                                                            <button class="btn btn-success" type="button" id="add_dynamic_field"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                                                        <?php } else { ?>                                            
                                                            <button class="btn btn-danger remove_dynamic_fields" type="button" data-fieldid="<?php echo ($i + 1); ?>"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span> </button>                                            
                                                        <?php } ?>    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>

                    <?php $i++;
                } ?> </div> <?php
            } else {
                ?>
                            <div id="education_fields">
                                <div class="col-sm-1 nopadding">
                                    <label class="control-label"><?php echo __('Option', ITG_TEXT_DOMAIN); ?> </label>
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
                <div class="col-md-12">
                    <div class=""><button type="button" class="btn btn-info btn-preview" data-toggle="modal" data-target="#preview-goal" >Preview</button></div>
                    <!-- Modal -->
                    <div id="preview-goal" class="modal fade" role="dialog">
                      <div class="modal-dialog modal-lg">
                        <?php 
                            $ccode = get_option('itg_currency_code');
                            $paypal = new AngellEYE_IfThenGive_PayPal_Helper();
                            $symbol = $paypal->get_currency_symbol($ccode);
                        ?>    
                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><?php _e('Your Goal will be Displayed like this.',ITG_TEXT_DOMAIN); ?></h4>
                          </div>
                          <div class="modal-body">
                              <div class="row">
                                  <div class="modal_itg_container">
                                      <div class="ifthengive_container">
                                        <div class="itg_post-item">                                               
                                            <div class="itg_post-title"><h3 class="trigger_name"></h3></div>
                                            <div class="itg_post-image">
                                                <img src="" class="image_url" width="100%">                                                
                                            </div>
                                            <div class="itg_post-content-details">
                                                <div class="itg_post-description">
                                                    <p class="trigger_desc"></p>
                                                </div>
                                                <div class="itg_post-title">
                                                    <div class="fixed_amount" style="display: none">
                                                        <h4 class="lead fixed_amount">
                                                        <?php _e('If ',ITG_TEXT_DOMAIN); ?> <span class="trigger_thing"> </span><?php _e(' Then I will Give ',ITG_TEXT_DOMAIN); ?> <?php echo $symbol; ?><span id="ifthengive_fixed_price_span"></span> 
                                                         </h4>
                                                    </div>
                                                </div>
                                                <div class="itg_post-title">
                                                    <div class="manual_amount" style="display: none">
                                                        <h4 class="lead manual_amount"><?php _e('If ',ITG_TEXT_DOMAIN); ?><span class="trigger_thing"></span><?php _e(' Then I will Give ',ITG_TEXT_DOMAIN); ?><?php echo $symbol; ?><span id="ifthengive_manual_price_span"></span></h4>                                                        
                                                    </div>
                                                </div>
                                                <div class="itg_post-title">
                                                    <div class="select_amount" style="display: none">
                                                        <h4 class="lead"><?php _e('If ',ITG_TEXT_DOMAIN); ?><span class="trigger_thing"></span><?php _e(' Then I will Give ',ITG_TEXT_DOMAIN); ?><?php echo $symbol; ?><span id="ifthengive_fixed_price_span_select"></span></h4>                                                        
                                                    </div>
                                                </div>
                                            </div>                                                            
                                        </div>
                                                           
                                        <div class="itgcontainer" id="ifthengive_signup_form">
                                            <div class="itg_hr-title itg_center">
                                                <abbr><?php _e('Sign up for',ITG_TEXT_DOMAIN); ?> <span class="trigger_name"></span></abbr>
                                            </div>
<!--                                            <p class="text-info"><?php _e('Instruction',ITG_TEXT_DOMAIN); ?></p>
                                            <ol>
                                                <li><?php _e('Lorem ipsum dolor sit amet',ITG_TEXT_DOMAIN); ?></li>
                                                <li><?php _e('Consectetur adipiscing elit',ITG_TEXT_DOMAIN); ?></li>
                                                <li><?php _e('Integer molestie lorem at massa',ITG_TEXT_DOMAIN); ?></li>
                                            </ol>-->
                                            <form method="post" name="signup" id="ifthengive_signup">                                                                                                
                                                <div class="itg_post-title">
                                                    <div class="manual_amount" style="display: none">                                                        
                                                        <div class="itg_form-group">
                                                            <label for="manualamout" class="control-label"><?php _e('Enter Amount',ITG_TEXT_DOMAIN); ?></label>
                                                            <input type="text" name="itg_manual_amount_input" value="50" class="itg_form-control" autocomplete="off" id="itg_manual_amount_input" placeholder="Enter Amount"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="itg_post-title">
                                                    <div class="select_amount" style="display: none">                                                        
                                                        <div class="itg_form-group">
                                                            <label class="itg_upper">Select</label>
                                                            <select class="itg_form-control" name="ifthengive_option_amount" id="ifthengive_option_amount"></select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="itg_form-group">
                                                    <label class="itg_upper"><?php _e('Frist Name',ITG_TEXT_DOMAIN); ?></label>
                                                    <input type="text" class="itg_form-control" name="ifthengive_firstname" id="ifthengive_firstname" required="required" value="" autocomplete="off">
                                                </div>
                                                <div class="itg_form-group">
                                                     <label for="itg_upper"><?php _e('Last Name',ITG_TEXT_DOMAIN); ?></label>
                                                      <input type="text" class="itg_form-control" name="ifthengive_lastname" id="ifthengive_lastname" required="required" value="" autocomplete="off">
                                                </div>
                                                 <div class="itg_form-group">
                                                    <label for="itg_upper"><?php _e('Email address',ITG_TEXT_DOMAIN); ?></label>
                                                    <input type="email" class="itg_form-control" name="ifthengive_email" id="ifthengive_email" required="required" value="" autocomplete="off">
                                                </div>
                                                <div class="itg_form-group">
                                                    <label for="itg_upper"><?php _e('Password',ITG_TEXT_DOMAIN); ?></label>
                                                    <input type="password" class="itg_form-control" name="ifthengive_password" id="ifthengive_password" required="required" autocomplete="off">
                                                </div>
                                                <div class="itg_form-group">
                                                    <label for="itg_upper"><?php _e('Re-type Password',ITG_TEXT_DOMAIN); ?></label>
                                                    <input type="password" class="itg_form-control" name="ifthengive_retype_password" id="ifthengive_retype_password" required="required" autocomplete="off">
                                                </div>
                                                <div class="itg_form-inline itg_form-group">
                                                    <button type="button" class="itg_btn itg_btn-primary" ><?php _e('Sign Up For',ITG_TEXT_DOMAIN); ?> <span class="trigger_name"></span></button>
                                                </div>  
                                                 
                                            </form>
                                        </div>                                                                                 
                                    </div>
                                  </div>
                              </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- End Modal -->
                </div>                
            </div>
            <script type="text/javascript">
                jQuery('#preview-goal').on('show.bs.modal', function () {
                    jQuery('#preview-goal .fixed_amount').css('display','none');
                    jQuery('#preview-goal .manual_amount').css('display','none');
                    jQuery('#preview-goal .select_amount').css('display','none');
                    var trigger_name = jQuery('input[name="post_title"]').val();                    
                    var image_url = jQuery('input[name="image_url"]').val();
                    var trigger_desc = jQuery('textarea[name="trigger_desc"]').val();
                    var fixed_radio = jQuery('input[name="fixed_radio"]:checked').val();
                    var trigger_thing = jQuery('input[name="trigger_thing"]').val();                     
                    jQuery('.trigger_thing').text(trigger_thing);
                    jQuery('#preview-goal .trigger_name').text(trigger_name);
                    jQuery('#preview-goal .image_url').attr('src',image_url);
                    jQuery('#preview-goal .trigger_desc').text(trigger_desc);
                   if(fixed_radio == 'fixed'){
                        jQuery('#preview-goal .fixed_amount').css('display','block');
                        famt = parseFloat(jQuery('input[name="fixed_amount_input"]').val()).toFixed(2);
                        if(isNaN(famt)){
                            jQuery('#ifthengive_fixed_price_span').text('0.00');
                        }
                        else{
                            jQuery('#ifthengive_fixed_price_span').text(famt);
                        }                        
                   }else if(fixed_radio == 'manual'){
                        jQuery('#preview-goal .manual_amount').css('display','block');
                        mamt = parseFloat(jQuery('input[name="manual_amount_input"]').val()).toFixed(2);
                        if(isNaN(mamt)){
                            jQuery('#ifthengive_manual_price_span').text('0.00');
                            jQuery('#itg_manual_amount_input').val('0.00');
                        }
                        else{
                            jQuery('#ifthengive_manual_price_span').text(mamt);
                            jQuery('#itg_manual_amount_input').val(mamt);
                        }
                        
                   }else{
                        jQuery('#preview-goal .select_amount').css('display','block');
                        var selectamt = parseFloat(jQuery('input[name="option_amount[]"]').val()).toFixed(2);
                        if(isNaN(selectamt)){
                            jQuery('#ifthengive_fixed_price_span_select').html('').html('50.00');
                        }else{
                            jQuery('#ifthengive_fixed_price_span_select').text(selectamt);
                        }                        
                        
                        var i = 0;
                        var option_amounts = jQuery('input[name="option_amount[]"]').val();
                        jQuery("#ifthengive_option_amount").html('');
                        jQuery('input[name="option_name[]"]').each(function() {
                            var option_name = jQuery(this).val();
                            var option_amount = parseFloat(jQuery('[id=option_amount]:eq('+i+')').val()).toFixed(2);
                            if(isNaN(option_amount)){
                                option_amount = '0.00';
                            }
                            jQuery("#ifthengive_option_amount").append(jQuery('<option>', { value: option_amount, text: option_name+'    '+option_amount }));
                            i++;
                        });
                   }
                   jQuery(document).on('keyup','#itg_manual_amount_input', function (){
                        var amt = parseFloat(jQuery(this).val()).toFixed(2);                        
                        if(isNaN(amt)){
                            jQuery('#ifthengive_manual_price_span').html('').html(mamt);
                        }else{
                            jQuery('#ifthengive_manual_price_span').html('').html(amt);
                        }                
                    });
                    jQuery(document).on('change','#ifthengive_option_amount', function (){
                        jQuery('#ifthengive_fixed_price_span_select').html('').html(jQuery(this).val());
                    });
                });
            </script>
        <?php
        }
    }

    /**
     * ifthengive_shortcode_interface_html function is for
     * html of interface when action is View.
     * @since 1.0.0
     * @access public
     */
    public static function ifthengive_shortcode_interface_html() {
        global $post, $post_ID;
        self::itg_admin_notice__success();
        $final='<div class="ifthengive_container">
            <div class="row">
                <div class="col-md-12">
                    <p>'. __("You can easily place this Goal in your pages and posts using this tool....", ITG_TEXT_DOMAIN).'</p>
                    <img src="'.ITG_PLUGIN_URL.'/admin/images/ifthengive_tool.png" class="img-responsive" style="margin: 0 auto;"/>
                </div>
            </div>
            <div class="row">
                <div class="text-center">'.__('OR', ITG_TEXT_DOMAIN).'</div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <p>'. __('You may copy and paste this shortcode into any Page or Post to place the "Goal" where you would like it to be displayed.', ITG_TEXT_DOMAIN).'</p>                    
                    <div class="ifthengive_highlight">
                        <h4 id="h4_clipboard">[ifthengive_goal id=' . $post_ID . ']</h4>
                        <span class="btn-clipboard" data-toggle="tooltip" data-placement="right" title="Copy To Clipboard">'. __('Copy', ITG_TEXT_DOMAIN).'</span>
                    </div>                    
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-info" href="'. admin_url().'post.php?post='. $_GET['post'] .'&action=edit">'. __('Back To Edit Goal',ITG_TEXT_DOMAIN).'</a>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(\'[data-toggle="tooltip"]\').tooltip();

            var clipboard = new Clipboard(\'#h4_clipboard,.btn-clipboard\', {
                target: function () {
                    return document.querySelector(\'.ifthengive_highlight h4\');
                }
            });
            / Below code will use whenever we want further clipboard work /
            clipboard.on(\'success\', function(e) {
                //console.log(e);                
                alertify.success(\'Shortcode is copied to your clipboard.\'); 
             });
                     
             /*clipboard.on(\'error\', function(e) {
             console.log(e);
             });*/
        </script>';
        echo apply_filters('itg_filter_goal_view',$final,$post_ID);
    }

    public static function ifthengive_givers_interface_html() {                            
        ?>
        <div class="wrap">            
            <div class="ifthengive_admin_container">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        $trigger_name = get_post_meta($_REQUEST['post'], 'trigger_name', true);
                        ?>
                        <div class="text-center"><img src="<?php echo ITG_PLUGIN_URL.'admin\images\icon.png' ?>" alt="IfThenGive"></div>    
                        <div class="itg_hr-title itg_hr-long itg_center"><abbr><?php echo __('Givers For ', ITG_TEXT_DOMAIN); ?><?php echo $trigger_name; ?></abbr></div>
                    </div>
                    <?php
                    if(self::is_My_Goal($_REQUEST['post'])){
                        $givers_count = AngellEYE_IfThenGive_Givers_Table::record_count();
                        //$is_all_givers_suspended = AngellEYE_IfThenGive_Givers_Table::check_all_givers_suspended($_REQUEST['post']);
                        if($givers_count > 0) {
                    ?>    
                        <div class="col-md-12 text-center">
                            <span class="itg_text-info"><?php echo __('Click ', ITG_TEXT_DOMAIN); ?><strong><?php _e('"Process Donation"',ITG_TEXT_DOMAIN); ?></strong><?php echo __(' Button to Capture your Transactions.', ITG_TEXT_DOMAIN); ?></span><br/>
                            <a  style="margin-top: 10px" class="btn itg_btn-primary btn-lg" id="ifthengive_fun" data-redirectUrl="<?php echo site_url(); ?>/wp-admin/edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post=<?php echo $_REQUEST['post']; ?>&view=DoTransactions" href="#" data-postid ="<?php echo $_REQUEST['post']; ?>" ><?php _e('Process Donation',ITG_TEXT_DOMAIN); ?></a>
                        </div>
                    <div class="hidden" id="div_goal_in_process">
                        <a href="<?php echo admin_url('edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post='.$_REQUEST["post"].'&view=DoTransactions&process=continue_old'); ?>" class="btn btn-warning"><?php _e('Continue with remaning',ITG_TEXT_DOMAIN) ?></a>
                        <a href="<?php echo admin_url('edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post='.$_REQUEST["post"].'&view=DoTransactions$process=start_over'); ?>" class="btn btn-primary"><?php _e('Start Over', ITG_TEXT_DOMAIN); ?></a>
                    </div>
                    
                    <?php                    
                        }
                    }
                    ?>                    
                </div>                            
                <div class="row">
                    <div class="col-md-12">                                     
        <?php
        $table = new AngellEYE_IfThenGive_Givers_Table();
        $table->prepare_items();
        echo '<form method="post" action="">';
        $table->search_box('Search', 'givers_search_id');
        $table->display();
        echo '</form>';
        ?>                    
                    </div>                
                </div>
            </div>   
        </div>   
        <?php        
    }
    
    public static function itg_admin_notice__success() {
        if(isset($_REQUEST['update_post_success']) && $_REQUEST['update_post_success'] == true){
    ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( 'Goal Updated Successfully',ITG_TEXT_DOMAIN); ?></p>
            </div>
            <?php
        }elseif(isset($_REQUEST['add_post_success']) && $_REQUEST['add_post_success'] == true){
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( 'Goal Added Successfully',ITG_TEXT_DOMAIN); ?></p>
            </div>
            <?php
        }
    }
    
    public static function ifthengive_do_transactions_interface_html() {                
        if(!self::is_My_Goal($_REQUEST['post'])){
            ?>
            <div class="wrap">
                <div class="ifthengive_admin_container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="notice notice-warning is-dismissible">
                                <p style="font-size: 22px;">
                                    <?php _e('Sorry , You are not allow to access this page.',ITG_TEXT_DOMAIN); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>        
        <?php
        }
        else{
        $in_process = get_option('itg_txns_in_process');
        $process = isset($_REQUEST['process']) ? $_REQUEST['process'] : '';        
        if($in_process === 'yes' && $process!='continue_old' && $process!= 'start_over') : ?>
            <div class="wrap">
                <div class="ifthengive_admin_container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="notice notice-warning is-dismissible">
                                <p style="font-size: 22px;"><?php _e('Other Donation Process are working. You can start when it gets over.!',ITG_TEXT_DOMAIN); ?></p>
                            </div>                         
                        </div>
                    </div>
                </div>
            </div>
        <?php
            exit;        
        endif;    
        @set_time_limit(ITG_PLUGIN_SET_TIME_LIMIT);
        @ignore_user_abort(true);
        $EmailString = '';       
        $goal_id = $_REQUEST['post'];               
        
        if(isset($_REQUEST['process']) && $_REQUEST['process'] === 'continue_old'){
            $givers = AngellEYE_IfThenGive_Givers_Table::get_remaining_process_givers($goal_id);
        }
        else{
            $givers = AngellEYE_IfThenGive_Givers_Table::get_all_givers();
        }
        $number_of_givers = count($givers);
        if($number_of_givers <= 0){ ?>
            <div class="wrap">
                <div class="ifthengive_admin_container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="notice notice-warning is-dismissible">
                                <p style="font-size: 22px;"><?php _e('No Givers to process.!',ITG_TEXT_DOMAIN); ?></p>
                            </div>                         
                        </div>
                    </div>
                </div>
            </div>
        <?php
            exit;
        }
        
        $trigger_name = get_post_meta($goal_id, 'trigger_name', true);
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);   
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        if (ob_get_level() == 0)
            ob_start();
        ?>
        <div class="wrap">
            <div class="ifthengive_admin_container">
                <div class="row">
                    <div class="text-center"><img src="<?php echo ITG_PLUGIN_URL.'admin\images\icon.png' ?>" alt="IfThenGive"></div>    
                    <div class="itg_hr-title itg_hr-long itg_center"><abbr><?php _e('Capturing Transactions',ITG_TEXT_DOMAIN); ?></abbr></div>
                        
                    <div class="col-md-12">                        
                                <div class="table-responsive">
        <?php                                
        $PayPal_config = new AngellEYE_IfThenGive_PayPal_Helper();        
        $PayPal_config->set_api_cedentials();                
        $PayPal_config->set_api_subject($goal_id);
        $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());        
        /*
         *   By default Angell EYE PayPal PHP Library has ButtonSource is "AngellEYE_PHPClass".
         *   We are overwirting that variable with "AngellEYE_IfThenGive" value.
         *   It also reflactes in NVPCredentials string so we are also replcing it.
         */
        $PayPal->APIButtonSource = ITG_BUTTON_SOURCE;
        $PayPal->NVPCredentials = str_replace('AngellEYE_PHPClass',ITG_BUTTON_SOURCE,$PayPal->NVPCredentials);        
        $ccode = get_option('itg_currency_code');        
        $symbol = $PayPal_config->get_currency_symbol($ccode);
        $total_txn = 0;
        $total_txn_success = 0;
        $total_txn_failed = 0;
        $total_amount=0;
        $total_amount_success=0;
        $total_amount_failed=0;           
        
        $headerString = '<div style="margin-right: -15px; margin-left: -15px;">
            <div style="width: 100%;">
                <div style="text-align: center;"><img src="'.ITG_PLUGIN_URL.'/admin/images/ifthengive.png" alt="IfThenGive"></div>
                <div style="width: 100%; margin: 10px auto 25px; float: none; height: auto; color: #f58634;text-align: center;">
                    <strong style="background-color: #ffffff; font-weight: 300; font-size:20px; padding:2px 10px; border-radius: 2px; position:relative; top:-10px;  letter-spacing:.2em;  text-transform:uppercase; border:none;
                          ">'. __('Transactions Report For ', ITG_TEXT_DOMAIN).__($trigger_name,ITG_TEXT_DOMAIN).'</strong></div>
            </div>
        </div>';
        ?>
        <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
              0%
            </div>
        </div>
                                    <?php
                                    ob_flush();
                                        flush();
                                    
       echo $EmailString.='<table style="                                
                                width: 100%;
                                max-width: 100%;
                                margin-bottom: 20px;
                                background-color: transparent;
                                border-spacing: 0;
                                border-collapse: collapse;
                                ">
                                            <tr style="background-color: #f9f9f9;">
                                                <th style="padding: 8px;line-height: 1.42857143;vertical-align: top;">'.__('Transaction ID',ITG_TEXT_DOMAIN).'</th>
                                                <th style="padding: 8px;line-height: 1.42857143;vertical-align: top;">'.__('Amount',ITG_TEXT_DOMAIN).'</th>
                                                <th style="padding: 8px;line-height: 1.42857143;vertical-align: top;">'.__('Payer Email',ITG_TEXT_DOMAIN).'</th>
                                                <th style="padding: 8px;line-height: 1.42857143;vertical-align: top;">'.__('Payment Status',ITG_TEXT_DOMAIN).'</th>
                                            </tr>';
                                        ob_flush();
                                        flush();
        update_option('itg_txns_in_process', 'yes');
        update_option('itg_current_process_goal_id', $goal_id);
        foreach ($givers as $value) {            
            if($total_txn %2== 0){
                $css = "";
            }
            else{
                $css = "background-color: #f9f9f9;";
            }
            
            $desc = !empty($trigger_name) ? $trigger_name : '';

            $DRTFields = array(
                'referenceid' => $value['BillingAgreement'],
                'paymentaction' => 'Sale',
            );

            $PaymentDetails = array(
                'amt' => number_format($value['amount'],2),
                'currencycode' => get_option('itg_currency_code'),
                'desc' => $desc                
            );

            $PayPalRequestData = array(
                'DRTFields' => $DRTFields,
                'PaymentDetails' => $PaymentDetails,
            );
            $PayPalResultDRT = $PayPal->DoReferenceTransaction($PayPalRequestData);
            $logArray = $PayPalResultDRT;
            $logArray['RAWREQUEST'] = $PayPal->MaskAPIResult($PayPalResultDRT['RAWREQUEST']);
            $logArray['REQUESTDATA'] = $PayPal->NVPToArray($logArray['RAWREQUEST']);
            //save log
            $debug = (get_option('itg_log_enable') == 'yes') ? 'yes' : 'no';
            
            $paypal_email = get_user_meta($value['user_id'], 'itg_gec_email', true);            
            if ($PayPalResultDRT['RAWRESPONSE'] !== false){            
                if ('yes' == $debug) {
                    $log_write = new AngellEYE_IfThenGive_Logger();
                    $log_write->add('angelleye_ifthengive_transactions', 'DoReferenceTransaction ' . $PayPalResultDRT['ACK'] . ' : ' . print_r($logArray, true), 'transactions');
                }
                if ($PayPal->APICallSuccessful($PayPalResultDRT['ACK'])) {

                    $total_txn_success++;
                    $total_amount_success += $value['amount'];
                    echo $trEmailString = "<tr style='".$css."'>
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($PayPalResultDRT['TRANSACTIONID'],ITG_TEXT_DOMAIN)."</td>
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".$symbol.__(number_format($PayPalResultDRT['AMT'],2),ITG_TEXT_DOMAIN)."</td>
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($paypal_email,ITG_TEXT_DOMAIN)."</td>                    
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($PayPalResultDRT['PAYMENTSTATUS'],ITG_TEXT_DOMAIN)."</td>
                    </tr>";
                    $EmailString.= $trEmailString;
                } else {
                    $total_txn_failed++;
                    $total_amount_failed += $value['amount'];
                    $PayPalResultDRT['TRANSACTIONID'] = '';

                    echo $trEmailString = "<tr style='".$css."'>
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>-</td>
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".$symbol.__(number_format($value['amount'],2),ITG_TEXT_DOMAIN)."</td>
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($paypal_email,ITG_TEXT_DOMAIN)."</td>                    
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($PayPalResultDRT['L_SHORTMESSAGE0'],ITG_TEXT_DOMAIN)."<br>".__("See ",ITG_TEXT_DOMAIN)."<a href='".admin_url('admin.php?page=ifthengive_option&tab=logs')."'>".__('logs',ITG_TEXT_DOMAIN)."</a>". __(' for more details',ITG_TEXT_DOMAIN)."</td>
                    </tr>";
                    $EmailString.= $trEmailString;
                }
                $new_post_id = wp_insert_post(array(
                    'post_status' => 'publish',
                    'post_type' => 'itg_transactions',
                    'post_title' => ('UserID:' . $value['user_id'] . '|GoalID:' . $goal_id . '|TxnID :' . $PayPalResultDRT['TRANSACTIONID'])
                        ));
                update_post_meta($new_post_id, 'itg_transactions_amount', number_format($value['amount'],2));
                update_post_meta($new_post_id, 'itg_transactions_wp_user_id', $value['user_id']);
                update_post_meta($new_post_id, 'itg_transactions_wp_goal_id', $goal_id);
                update_post_meta($new_post_id, 'itg_transactions_transaction_id', $PayPalResultDRT['TRANSACTIONID']);
                update_post_meta($new_post_id, 'itg_transactions_ack', $PayPalResultDRT['ACK']);
                update_post_meta($new_post_id, 'itg_signup_in_sandbox', $sandbox);
                update_post_meta($new_post_id, 'itg_txn_pt_status', '0');
                update_post_meta($value['signup_postid'], 'itg_transaction_status', '1');                        
            }
            else{
                $total_txn_failed++;
                $total_amount_failed += $value['amount'];
                $PayPalResultDRT['TRANSACTIONID'] = '';
                echo $trEmailString = "<tr style='".$css."'>
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>-</td>
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".$symbol.__(number_format($value['amount'],2),ITG_TEXT_DOMAIN)."</td>
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($paypal_email,ITG_TEXT_DOMAIN)."</td>                    
                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__('Internal Server Error.',ITG_TEXT_DOMAIN)."<br>".__("See ",ITG_TEXT_DOMAIN)."<a href='".admin_url('admin.php?page=ifthengive_option&tab=logs')."'>".__('logs',ITG_TEXT_DOMAIN)."</a>". __(' for more details',ITG_TEXT_DOMAIN)."</td>
                    </tr>";
                    $EmailString.= $trEmailString;
                    
                
            }
                ?>
                                        <?php
                                        $total_txn++;
                                        $progress = round(($total_txn * 100)/$number_of_givers);
                                        update_option('itg_current_process_progress', $progress);                                                                                        
                                        ?>
                                        <script>                                            
                                            jQuery('.progress-bar').css('width','<?php echo $progress; ?>%');
                                            jQuery('.progress-bar').attr('aria-valuenow','<?php echo $progress; ?>');
                                            jQuery('.progress-bar').html('<?php echo $progress; ?>%');                                            
                                        </script>
                                        <?php
                                        if($progress == 100){ ?>
                                            <script>
                                                jQuery('.progress-bar').addClass('progress-bar-success');
                                            </script>
                                            <?php
                                            update_option('itg_transaction_complete', 'yes');
                                            add_action("admin_notices", array('IfThenGive_Admin', 'processing_notice'));
                                            update_option('itg_txns_in_process', 'no');
                                        }
                                        $total_amount += $value['amount'];
                                        ob_flush();
                                        flush();
                                        sleep(1);
                                        /* we can uncomment above line if necesory because sleep will wait for 2 seconds on every iteration */
                                    }
                                    ?>              <?php echo $endtabeEmailString = "</table>";
                            $EmailString.=$endtabeEmailString; ?>
                                </div>
                            
                        
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo $alert_info_email_string = '<div style="padding: 15px;margin-bottom: 20px;border: 1px solid transparent;color: #31708f;background-color: #d9edf7;border-color: #bce8f1; border-radius: 4px; ">
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Transactions : ',ITG_TEXT_DOMAIN).'<strong>' . $total_txn . '</strong></p>
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Successful Transactions : ',ITG_TEXT_DOMAIN).'<strong>' . $total_txn_success . '</strong></p>
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Failed Transactions : ',ITG_TEXT_DOMAIN).'<strong>' . $total_txn_failed . '</strong></p>
                    <hr style="box-sizing: content-box;height: 0;margin-top: 20px;margin-bottom: 20px;border: 0;border-top: 1px solid #eee;border-top-color: #a6e1ec;">    
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Transactions Amount : ',ITG_TEXT_DOMAIN).'<strong>' . $symbol.number_format($total_amount,2) . '</strong></p>
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Successful Transactions Amount : ',ITG_TEXT_DOMAIN).'<strong>' . $symbol.number_format($total_amount_success,2) . '</strong></p> 
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Failed Transactions Amount  : ',ITG_TEXT_DOMAIN).'<strong>' . $symbol.number_format($total_amount_failed,2) . '</strong></p>    
                </div>';
                        $EmailString.=$alert_info_email_string;       
                        if($total_txn > 0 ){                                                       
                            $headers = "From: IfThenGive <info@ifthengive.com> \r\n";
                            $headers .= "Reply-To: noreply@ifthengive.com \r\n";
                            //$headers .= "CC: ifthengive@ifthengive.com\r\n";
                            $headers .= "MIME-Version: 1.0\r\n";
                            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                            $to = $admin_email = get_option('admin_email');
                            $subject = __('IfThenGive Transaction Report For ' . $trigger_name,ITG_TEXT_DOMAIN);
                            $message = $headerString.$EmailString;

                            $dompdf->load_html($message);
                            $dompdf->set_paper("A4", "portrait");
                            $dompdf->render();
                            $output  = $dompdf->output(); // to Download PDF
                            $filename = sanitize_file_name($trigger_name.'_transaction_report_'.time().'.pdf');
                            file_put_contents(ITG_LOG_DIR.'/'.$filename, $output);  
                            $attachments = array( ITG_LOG_DIR . '/'.$filename );
                            wp_mail($to, $subject, $headerString.$alert_info_email_string, $headers,$attachments);                                                        
                            unlink(ITG_LOG_DIR . '/'.$filename);
                        }
                        ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <a class="btn btn-info" href="<?php echo site_url() . '/wp-admin/edit.php?post_type=ifthengive_goals'; ?>"><?php _e('Back To Goals',ITG_TEXT_DOMAIN); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        ob_end_flush();
       }
    }
    
    public static function ifthengive_list_transactions_interface_html() {        
        if(!self::is_My_Goal($_REQUEST['post'])){
            ?>
            <div class="wrap">
                <div class="ifthengive_admin_container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                         <?php _e('Sorry , You are not allow to access this page.',ITG_TEXT_DOMAIN); ?>
                        </div>
                    </div>
                </div>
            </div>        
        <?php
        }
        else{
        ?>
        <div class="wrap">
            <div class="ifthengive_admin_container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <?php
                        $trigger_name = get_post_meta($_REQUEST['post'], 'trigger_name', true);
                        ?>
                        <div class="text-center"><img src="<?php echo ITG_PLUGIN_URL.'admin\images\icon.png' ?>" alt="IfThenGive"></div>    
                        <div class="itg_hr-title itg_hr-long itg_center"><abbr><?php _e('Transactions for ',ITG_TEXT_DOMAIN); ?> <?php echo __($trigger_name,ITG_TEXT_DOMAIN) ; ?></abbr></div>                        
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form method="post">
                            <?php
                            $table = new AngellEYE_IfThenGive_Transactions_Table();
                            $table->prepare_items();
                            $table->search_box('Search', 'givers_transaction_search_id');
                            $table->display();
                            ?>
                        </form>
                    </div>                
                </div>
            </div>        
        </div>
        <?php
        }
    }

    public static function ifthengive_get_transaction_detail_html() {
        if(!self::is_My_Goal($_REQUEST['post'])){
            ?>
            <div class="wrap">
                <div class="ifthengive_admin_container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                         <?php _e('Sorry , You are not allow to access this page.',ITG_TEXT_DOMAIN); ?>
                        </div>
                    </div>
                </div>
            </div>        
        <?php
        }
        else{        
        $transaction_id = $_REQUEST['txn_id'];        
        $goal_id = $_REQUEST['post'];
        $givers = AngellEYE_IfThenGive_Givers_Table::get_all_givers();
        $PayPal_config = new AngellEYE_IfThenGive_PayPal_Helper();
        $PayPal_config->set_api_cedentials();        
        $PayPal_config->set_api_subject($goal_id);
        $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
        /*
         *   By default Angell EYE PayPal PHP Library has ButtonSource is "AngellEYE_PHPClass".
         *   We are overwirting that variable with "AngellEYE_IfThenGive" value.
         *   It also reflactes in NVPCredentials string so we are also replcing it.
         */  
        $PayPal->APIButtonSource = ITG_BUTTON_SOURCE;
        $PayPal->NVPCredentials = str_replace('AngellEYE_PHPClass',ITG_BUTTON_SOURCE,$PayPal->NVPCredentials);        
        $trigger_name = get_post_meta($goal_id, 'trigger_name',true);
        $GTDFields = array(
            'transactionid' => $transaction_id
        );
        $PayPalRequestData = array('GTDFields' => $GTDFields);
        $PayPalResultTransactionDetail = $PayPal->GetTransactionDetails($PayPalRequestData);        
        if($PayPalResultTransactionDetail['RAWRESPONSE'] == false){
            ?>
                <div class="wrap">
                    <div class="ifthengive_admin_container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" role="alert">
                                    <h4 class="alert-heading"><?php _e('Error..!',ITG_TEXT_DOMAIN); ?></h4>
                                    <p><?php _e('PayPal Timout Error occured.',ITG_TEXT_DOMAIN); ?></p>
                                    <?php
                                        if(isset($PayPalResultTransactionDetail['ERRORS'])){
                                            $PayPal->DisplayErrors($PayPalResultTransactionDetail['ERRORS']);
                                        }                                        
                                    ?>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            exit;    
        }                    
        if ($PayPal->APICallSuccessful($PayPalResultTransactionDetail['ACK'])) {
            ?>
              <div class="wrap">
                <div class="ifthengive_admin_container">                                    
                    <div class="row">
                        <div class="text-center"><img src="<?php echo ITG_PLUGIN_URL.'admin\images\icon.png' ?>" alt="IfThenGive"></div>    
                        <div class="itg_hr-title itg_hr-long itg_center"><abbr><?php _e('Transaction Id ',ITG_TEXT_DOMAIN); ?> <?php echo '#' . $_REQUEST['txn_id']; ?></abbr></div>                            
                        <div class="col-md-10">
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Payer Email :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['EMAIL']) ? $PayPalResultTransactionDetail['EMAIL']: ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Payer ID :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['PAYERID']) ? $PayPalResultTransactionDetail['PAYERID'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Country Code :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['COUNTRYCODE']) ? $PayPalResultTransactionDetail['COUNTRYCODE'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Goal Name :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($trigger_name) ? $trigger_name : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Payer PayPal Name :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php 
                                        $fname = isset($PayPalResultTransactionDetail['FIRSTNAME']) ? $PayPalResultTransactionDetail['FIRSTNAME'] : '';
                                        $lname = isset($PayPalResultTransactionDetail['LASTNAME']) ? $PayPalResultTransactionDetail['LASTNAME'] : '';
                                        echo  $fname. ' ' .$lname ; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Transaction ID :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['TRANSACTIONID']) ? $PayPalResultTransactionDetail['TRANSACTIONID'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Transaction Type :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['TRANSACTIONTYPE']) ? $PayPalResultTransactionDetail['TRANSACTIONTYPE'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Payment Type :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['PAYMENTTYPE']) ? $PayPalResultTransactionDetail['PAYMENTTYPE'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Amount :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['AMT']) ? number_format($PayPalResultTransactionDetail['AMT'],2) : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Payment Status :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['PAYMENTSTATUS']) ? $PayPalResultTransactionDetail['PAYMENTSTATUS'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Pending Reason :',ITG_TEXT_DOMAIN); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['PENDINGREASON']) ? $PayPalResultTransactionDetail['PENDINGREASON'] : ''; ?>
                                    </div>                                                                        
                                    <div class="clearfix"></div>                                                                                   
                                    <div class="col-md-6">
                                        <a class="btn btn-info" href="<?php echo admin_url('edit.php?post_type=ifthengive_goals'); ?>"><?php _e('Back To Goals',ITG_TEXT_DOMAIN); ?></a>
                                        <a class="btn btn-info" href="<?php echo admin_url('edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post='.$goal_id.'&view=ListTransactions'); ?>"><?php _e('Back To Transactions',ITG_TEXT_DOMAIN); ?></a>
                                    </div>
                        </div>                                        
                    </div>                     
                </div>        
            </div>
            <?php
            }
        else {
            ?>
                <div class="wrap">
                    <div class="ifthengive_admin_container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" role="alert">
                                    <h4 class="alert-heading"><?php _e('Error..!',ITG_TEXT_DOMAIN); ?></h4>
                                    <?php $PayPal->DisplayErrors($PayPalResultTransactionDetail['ERRORS']); ?>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
        <?php
        }
      }
    }

    public static function ifthengive_retry_failed_transactions_interface_html() {                    
        if(!self::is_My_Goal($_REQUEST['post'])){
            ?>
            <div class="wrap">
                <div class="ifthengive_admin_container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="notice notice-warning is-dismissible">
                                <p style="font-size: 22px;">
                                    <?php _e('Sorry , You are not allow to access this page.',ITG_TEXT_DOMAIN); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>        
        <?php
        }
        else{ 
        $in_process = get_option('itg_txns_in_process');
        $process = isset($_REQUEST['process']) ? $_REQUEST['process'] : '';
        if($in_process === 'yes' && $process!='continue_old' && $process!= 'start_over') { ?>
            <div class="wrap">
                <div class="ifthengive_admin_container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="notice notice-warning is-dismissible">
                                <p style="font-size: 22px;"><?php _e('Other Donation Process are working. You can start when it gets over.!',ITG_TEXT_DOMAIN); ?></p>
                            </div>                         
                        </div>
                    </div>
                </div>
            </div>
        <?php
            exit;        
        }
        @set_time_limit(ITG_PLUGIN_SET_TIME_LIMIT);
        @ignore_user_abort(true);
        $EmailString = '';        
        $goal_id = $_REQUEST['post'];
        $trigger_name = get_post_meta($goal_id, 'trigger_name', true);
        if(isset($_REQUEST['process']) && $_REQUEST['process'] === 'continue_old'){
            $givers = AngellEYE_IfThenGive_Transactions_Table::get_remaining_process_failed_givers($goal_id);
        }
        else{
            $givers = AngellEYE_IfThenGive_Transactions_Table::get_all_failed_givers($goal_id);
        }
        $number_of_givers = count($givers);
        if($number_of_givers <= 0) { ?>
            <div class="wrap">
                <div class="ifthengive_admin_container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="notice notice-warning is-dismissible">
                                <p style="font-size: 22px;"><?php _e('No Givers to process.!',ITG_TEXT_DOMAIN); ?></p>
                            </div>                         
                        </div>
                    </div>
                </div>
            </div>
        <?php
        exit;
        }
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);        
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        if (ob_get_level() == 0)
            ob_start();
        ?>
        <div class="wrap">
            <div class="ifthengive_admin_container">
                <div class="row">
                    <div class="text-center"><img src="<?php echo ITG_PLUGIN_URL.'admin\images\icon.png' ?>" alt="IfThenGive"></div>    
                    <div class="itg_hr-title itg_hr-long itg_center"><abbr><?php _e('Capturing Failure Payments',ITG_TEXT_DOMAIN); ?></abbr></div>                    
                    <div class="col-md-12">                        
                                <div class="table-responsive">
                                    <?php                                                                                                            
                                    $PayPal_config = new AngellEYE_IfThenGive_PayPal_Helper();                                    
                                    $PayPal_config->set_api_cedentials();                                     
                                    $PayPal_config->set_api_subject($goal_id);
                                    $ccode = get_option('itg_currency_code');        
                                    $symbol = $PayPal_config->get_currency_symbol($ccode);
                                    $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
                                    /*
                                    *   By default Angell EYE PayPal PHP Library has ButtonSource is "AngellEYE_PHPClass".
                                    *   We are overwirting that variable with "AngellEYE_IfThenGive" value.
                                    *   It also reflactes in NVPCredentials string so we are also replcing it.
                                    */
                                    $PayPal->APIButtonSource = ITG_BUTTON_SOURCE;
                                    $PayPal->NVPCredentials = str_replace('AngellEYE_PHPClass',ITG_BUTTON_SOURCE,$PayPal->NVPCredentials);        
                                    $total_txn = 0;
                                    $total_txn_success = 0;
                                    $total_txn_failed = 0;
                                    $total_amount=0;
                                    $total_amount_success=0;
                                    $total_amount_failed=0;                                      
                                    $headerString = '<div style="margin-right: -15px; margin-left: -15px;">
            <div style="width: 100%;">
                <div style="text-align: center;"><img src="'.ITG_PLUGIN_URL.'/admin/images/ifthengive.png" alt="IfThenGive"></div>
                <div style="width: 100%; margin: 10px auto 25px; float: none; height: auto; color: #f58634;text-align: center;">
                    <strong style="background-color: #ffffff; font-weight: 300; font-size:20px; padding:2px 10px; border-radius: 2px; position:relative; top:-10px;  letter-spacing:.2em;  text-transform:uppercase; border:none;
                          ">'. __('Retried Transactions Report For ', ITG_TEXT_DOMAIN).__($trigger_name,ITG_TEXT_DOMAIN).'</strong></div>
            </div>
        </div>';
                                    ?>
        <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
              0%
            </div>
        </div>
                                    <?php
                                    ob_flush();
                                        flush();
                            echo $EmailString.='<table style="                                
                                                        width: 100%;
                                                        max-width: 100%;
                                                        margin-bottom: 20px;
                                                        background-color: transparent;
                                                        border-spacing: 0;
                                                        border-collapse: collapse;
                                                        ">
                                            <tr style="background-color: #f9f9f9;">
                                                <th style="padding: 8px;line-height: 1.42857143;vertical-align: top;">'.__('Transaction ID',ITG_TEXT_DOMAIN).'</th>
                                                <th style="padding: 8px;line-height: 1.42857143;vertical-align: top;">'.__('Amount',ITG_TEXT_DOMAIN).'</th>
                                                <th style="padding: 8px;line-height: 1.42857143;vertical-align: top;">'.__('Payer Email',ITG_TEXT_DOMAIN).'</th>                                                
                                                <th style="padding: 8px;line-height: 1.42857143;vertical-align: top;">'.__('Payment Status',ITG_TEXT_DOMAIN).'</th>
                                            </tr>';
                                     ob_flush();
                                        flush();
                                    update_option('itg_txns_in_process', 'yes');
                                    update_option('itg_failed_txns_in_process', 'yes');
                                    update_option('itg_current_process_goal_id', $goal_id);
                                    foreach ($givers as $value) {                                            
                                        if($total_txn %2== 0){
                                            $css = "";
                                        }
                                        else{
                                            $css = "background-color: #f9f9f9;";
                                        }                                        
                                        $desc = !empty($trigger_name) ? $trigger_name : '';

                                        $DRTFields = array(
                                            'referenceid' => $value['BillingAgreement'],
                                            'paymentaction' => 'Sale',
                                        );

                                        $PaymentDetails = array(
                                            'amt' => $value['amount'],
                                            'currencycode' => get_option('itg_currency_code'),
                                            'desc' => $desc                                            
                                        );

                                        $PayPalRequestData = array(
                                            'DRTFields' => $DRTFields,
                                            'PaymentDetails' => $PaymentDetails,
                                        );
                                        $PayPalResultDRT = $PayPal->DoReferenceTransaction($PayPalRequestData);
                                        $logArray = $PayPalResultDRT;
                                        $logArray['RAWREQUEST'] = $PayPal->MaskAPIResult($PayPalResultDRT['RAWREQUEST']);
                                        $logArray['REQUESTDATA'] = $PayPal->NVPToArray($logArray['RAWREQUEST']);
                                        //save log
                                        $debug = (get_option('itg_log_enable') == 'yes') ? 'yes' : 'no';
                                        if ('yes' == $debug) {
                                            $log_write = new AngellEYE_IfThenGive_Logger();
                                            $log_write->add('angelleye_ifthengive_transactions', 'DoReferenceTransaction ' . $PayPalResultDRT['ACK'] . ' : ' . print_r($logArray, true), 'transactions');
                                        }
                                        $paypal_email = get_user_meta($value['user_id'], 'itg_gec_email', true);
                                        if ($PayPalResultDRT['RAWRESPONSE'] !== false){                                                                                    
                                            if ($PayPal->APICallSuccessful($PayPalResultDRT['ACK'])) {
                                                update_post_meta($value['post_id'], 'itg_transactions_transaction_id', $PayPalResultDRT['TRANSACTIONID']);
                                                $total_txn_success++;
                                                $total_amount_success += $value['amount'];
                                                echo $trEmailString = "<tr style='".$css."'>
                                                                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($PayPalResultDRT['TRANSACTIONID'],ITG_TEXT_DOMAIN)."</td>
                                                                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".$symbol.__(number_format($PayPalResultDRT['AMT'],2),ITG_TEXT_DOMAIN)."</td>
                                                                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($paypal_email,ITG_TEXT_DOMAIN)."</td>
                                                                        <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($PayPalResultDRT['PAYMENTSTATUS'],ITG_TEXT_DOMAIN)."</td>
                                                                    </tr>";                                            
                                                $EmailString.= $trEmailString;
                                            } else {
                                                $total_txn_failed++;
                                                $total_amount_failed += $value['amount'];
                                                $PayPalResultDRT['TRANSACTIONID'] = '';
                                                 echo $trEmailString = "<tr style='".$css."'>
                                                    <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>-</td>
                                                    <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".$symbol.__(number_format($value['amount'],2),ITG_TEXT_DOMAIN)."</td>
                                                    <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($paypal_email,ITG_TEXT_DOMAIN)."</td>                                                
                                                    <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($PayPalResultDRT['L_SHORTMESSAGE0'],ITG_TEXT_DOMAIN)."<br>".__("See ",ITG_TEXT_DOMAIN)."<a href='".admin_url('admin.php?page=ifthengive_option&tab=logs')."'>".__('logs',ITG_TEXT_DOMAIN)."</a>". __(' for more details',ITG_TEXT_DOMAIN)."</td>
                                                </tr>";
                                                $EmailString.= $trEmailString;
                                            }
                                            update_post_meta($value['post_id'], 'itg_transactions_ack', $PayPalResultDRT['ACK']);
                                            update_post_meta($value['post_id'], 'itg_signup_in_sandbox', $sandbox);
                                            update_post_meta($value['post_id'], 'itg_txn_pt_status', '1');
                                        }
                                        else{
                                            $total_txn_failed++;
                                            $total_amount_failed += $value['amount'];
                                            $PayPalResultDRT['TRANSACTIONID'] = '';
                                             echo $trEmailString = "<tr style='".$css."'>
                                                <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>-</td>
                                                <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".$symbol.__(number_format($value['amount'],2),ITG_TEXT_DOMAIN)."</td>
                                                <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__($paypal_email,ITG_TEXT_DOMAIN)."</td>                                                
                                                <td style='padding: 8px;line-height: 1.42857143;vertical-align: top;'>".__('Internal Server Error occured.',ITG_TEXT_DOMAIN)."<br>".__("See ",ITG_TEXT_DOMAIN)."<a href='".admin_url('admin.php?page=ifthengive_option&tab=logs')."'>".__('logs',ITG_TEXT_DOMAIN)."</a>". __(' for more details',ITG_TEXT_DOMAIN)."</td>
                                            </tr>";
                                            $EmailString.= $trEmailString;
                                        }
                                            ?>
                                        <?php
                                        $total_txn++;
                                         $progress = round(($total_txn * 100)/$number_of_givers);
                                         update_option('itg_current_process_progress', $progress);
                                        ?>
                                        <script>                                            
                                            jQuery('.progress-bar').css('width','<?php echo $progress; ?>%');
                                            jQuery('.progress-bar').attr('aria-valuenow','<?php echo $progress; ?>');
                                            jQuery('.progress-bar').html('<?php echo $progress; ?>%');
                                        </script>
                                        <?php
                                        if($progress == 100){ ?>
                                            <script>
                                                jQuery('.progress-bar').addClass('progress-bar-success');
                                            </script>
                                            <?php
                                            update_option('itg_transaction_complete', 'yes');
                                            add_action("admin_notices", array('IfThenGive_Admin', 'processing_notice'));
                                            update_option('itg_txns_in_process', 'no');
                                        }
                                        $total_amount += $value['amount'];
                                        ob_flush();
                                        flush();
                                        sleep(1);
                                        /* we can uncomment above line if necesory because sleep will wait for 2 seconds on every iteration */
                                    }
                                    ?>              <?php echo $endtabeEmailString = "</table>";
                            $EmailString.=$endtabeEmailString; ?>
                                </div>                            
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                         echo $alert_info_email_string = '<div style="padding: 15px;margin-bottom: 20px;border: 1px solid transparent;color: #31708f;background-color: #d9edf7;border-color: #bce8f1; border-radius: 4px; ">
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Transactions : ',ITG_TEXT_DOMAIN).'<strong>' . $total_txn . '</strong></p>
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Successful Transactions : ',ITG_TEXT_DOMAIN).'<strong>' . $total_txn_success . '</strong></p>
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Failed Transactions : ',ITG_TEXT_DOMAIN).'<strong>' . $total_txn_failed . '</strong></p>
                    <hr style="box-sizing: content-box;height: 0;margin-top: 20px;margin-bottom: 20px;border: 0;border-top: 1px solid #eee;border-top-color: #a6e1ec;">    
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Transactions Amount : ',ITG_TEXT_DOMAIN).'<strong>' . $symbol.number_format($total_amount,2) . '</strong></p>
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Successful Transactions Amount : ',ITG_TEXT_DOMAIN).'<strong>' . $symbol.number_format($total_amount_success,2) . '</strong></p> 
                    <p style="margin: 0 0 10px;margin-bottom: 0;">'.__('Total Failed Transactions Amount  : ',ITG_TEXT_DOMAIN).'<strong>' . $symbol.number_format($total_amount_failed,2) . '</strong></p>    
                </div>';   
                        if($total_txn > 0){
                            $EmailString.=$alert_info_email_string;

                            $headers = "From: IfThenGive <info@ifthengive.com> \r\n";
                            $headers .= "Reply-To: noreply@ifthengive.com \r\n";
                            //$headers .= "CC: ifthengive@ifthengive.com\r\n";
                            $headers .= "MIME-Version: 1.0\r\n";
                            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                            $to = $admin_email = get_option('admin_email');
                            $subject = __('Retried Transaction Report For ' . $trigger_name,ITG_TEXT_DOMAIN);
                            $message = $headerString.$EmailString;
                            $dompdf->load_html($message);
                            $dompdf->set_paper("A4", "portrait");
                            $dompdf->render();
                            $output  = $dompdf->output(); // to Download PDF
                            $filename = sanitize_file_name($trigger_name.'_transaction_report_'.time().'.pdf');
                            file_put_contents(ITG_LOG_DIR.'/'.$filename, $output);  
                            $attachments = array( ITG_LOG_DIR . '/'.$filename );
                            wp_mail($to, $subject, $headerString.$alert_info_email_string, $headers,$attachments);
                            unlink(ITG_LOG_DIR . '/'.$filename);
                        }
                        ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <a class="btn btn-info" href="<?php echo site_url() . '/wp-admin/edit.php?post_type=ifthengive_goals'; ?>"><?php _e('Back To Goals',ITG_TEXT_DOMAIN) ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        ob_end_flush();
        }
    }
    
    public static function ifthengive_disconnect_interface_html() {
        
        if($_GET['env']=='sandbox'){
            delete_option('itg_permission_sb_connected_person_merchant_id');
            delete_option('itg_sb_api_credentials_username');
            delete_option('itg_sb_api_credentials_password');
            delete_option('itg_sb_api_credentials_signature');
            update_option('itg_sb_connected_to_paypal', 'no');
            delete_option('itg_sb_api_credentials_addded_manually');
        }
        if($_GET['env']=='live'){
            delete_option('itg_permission_lv_connected_person_merchant_id');
            delete_option('itg_lv_api_credentials_username');
            delete_option('itg_lv_api_credentials_password');
            delete_option('itg_lv_api_credentials_signature');
            update_option('itg_live_connected_to_paypal', 'no');
            delete_option('itg_lv_api_credentials_addded_manually');
        }        
        
        
        $url = admin_url('admin.php?page=ifthengive_option&tab=connect_to_paypal');
        echo "<script>";
        echo 'window.location.href = "' . $url . '";';
        echo "</script>";
        die();
    }

    public static function ifthengive_get_users_transactions_interface_html(){
        if(!self::is_My_Goal($_REQUEST['post'])){
            ?>
            <div class="wrap">
                <div class="ifthengive_admin_container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                         <?php _e('Sorry , You are not allow to access this page.',ITG_TEXT_DOMAIN); ?>
                        </div>
                    </div>
                </div>
            </div>        
        <?php
        }
        else{  
        ?>
        <div class="wrap">
            <div class="ifthengive_admin_container">
                <div class="row">
                    <div class="text-center"><img src="<?php echo ITG_PLUGIN_URL.'admin\images\icon.png' ?>" alt="IfThenGive"></div>
                    <?php if(isset($_REQUEST['user_id'])){ 
                          $user_info = get_userdata($_REQUEST['user_id']);  
                    ?>
                    <div class="itg_hr-title itg_hr-long itg_center"><abbr><?php _e('Transactions Of '.$user_info->display_name,ITG_TEXT_DOMAIN); ?></abbr></div>
                    <div class="col-md-12">
                        <form method="post">
                            <?php
                            $table = new AngellEYE_IfThenGive_users_Transactions_Table();
                            $table->prepare_items();
                            $table->search_box('Search', 'givers_users_transaction_search_id');
                            $table->display();
                            ?>
                        </form>
                    </div>
                    <?php } ?>                        
                </div>
            </div>
        </div>
        <?php
        }
    }
    
    public static function ifthengive_hide_publish_button_until() {
        if (isset($_REQUEST['post_type'])) {
            if ($_REQUEST['post_type'] == 'ifthengive_goals') {
                $connect_to_sandbox_paypal_flag = get_option('itg_sb_connected_to_paypal');
                $connect_to_live_paypal_flag = get_option('itg_live_connected_to_paypal');
                if ($connect_to_sandbox_paypal_flag != 'Yes' && $connect_to_live_paypal_flag != 'Yes') {
                    ?>
                    <style>
                        #publishing-action { display: none; }
                        #save-action{display: none;}
                    </style>
                    <?php
                }
            }
        }
        if(isset($_REQUEST['action']) && isset($_REQUEST['view']) && $_REQUEST['action']=== 'edit' && $_REQUEST['view'] =='true'){
            ?>
                    <style>
                        #publishing-action { display: none; }
                        #save-action{display: none;}
                    </style>
                    <?php
        }        
    }
    
    public static function cancel_billing_agreement_giver() {
        if (isset($_POST['userid'])) {
            $user_id = $_POST['userid'];
            $data = get_user_meta($user_id,'itg_giver_'.$_POST['postid'].'_status',true);
            if(empty($data)){
               update_user_meta( $user_id , 'itg_giver_'.$_POST['postid'].'_status', 'suspended' );
            }
            elseif($data == 'suspended'){
                update_user_meta( $user_id , 'itg_giver_'.$_POST['postid'].'_status', 'active' );
            }
            else{
                update_user_meta( $user_id , 'itg_giver_'.$_POST['postid'].'_status', 'suspended' );
            }
        }
        exit;
    }
    
    public static function is_My_Goal($goal_id){
        
        if ( current_user_can( 'manage_options' ) ) {
            /* return true if current user is administrator, he can access everything */
            return true;
        }
            
        global $current_user;
        $post = get_post($goal_id);	
	if (is_user_logged_in() && $current_user->ID == $post->post_author)  {
	    /* You are author of this goal */
            return true;
	}
        else{
            /* You are not author of this goal */
            return false;
        }
    }
    
    public static function check_goal_is_in_process(){
        $process_button_postid = isset($_POST['post_id']) ? $_POST['post_id'] : '';
        $in_process = get_option('itg_txns_in_process');
        $current_process_goal = get_option('itg_current_process_goal_id');
        $complete_percentage = get_option('itg_current_process_progress');
        if($in_process === 'yes') {
            if($process_button_postid == $current_process_goal){
                echo json_encode( array (
                        'in_process' => 'true',
                        'same_goal' => 'true'                
                    ));
                exit;
            }
            else{
                echo json_encode( array (
                    'in_process' => 'true',
                    'same_goal' => 'false'                
                ));
                exit;
            }
        }
        else{
            echo json_encode( array (
                    'in_process' => 'false'                    
                ));
            exit;
        }        
    }
    
    public static function delete_giver_from_goal(){
       $signup_postid = $_POST['signup_postid'];
       $user_id = $_POST['userid'];
       $goal_id = $_POST['goal_id'];
       
       // just update it with blank so user data will not delete forever.
       update_post_meta($signup_postid, 'itg_signup_amount', '');
       update_post_meta($signup_postid, 'itg_signup_wp_user_id', '');
       update_post_meta($signup_postid, 'itg_signup_wp_goal_id', '');
       update_post_meta($signup_postid, 'itg_signup_in_sandbox', '');
       update_post_meta($signup_postid, 'itg_transaction_status', '');
       
       update_user_meta( $user_id , 'itg_giver_'.$goal_id.'_status', '' );
       
       $itg_signedup_goals = get_user_meta($user_id, 'itg_signedup_goals');
       $goalArray = explode('|', $itg_signedup_goals[0]);
        if(!empty($goalArray)){
            if(in_array($goal_id, $goalArray)){
                $array_without_goal = array_diff($goalArray, array($goal_id));
                if(empty($array_without_goal)){                    
                    update_user_meta($user_id, 'itg_signedup_goals', '');
                }
                else{
                    $goals_string = implode("|",$array_without_goal);
                    update_user_meta($user_id, 'itg_signedup_goals', $goals_string);
                }
                
            }
        }
        $trigger_name = get_post_meta($goal_id, 'trigger_name', true);
        $user = get_userdata($user_id);        
        $EmailHeader = '<div dir="ltr" style="background-color: rgb(245, 245, 245); margin: 0; padding: 70px 0 70px 0; width: 100%; height:100%">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" min-height="100%">
                                <tbody>
                                    <tr>
                                        <td align="center" valign="top">
                                            <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: rgb(253, 253, 253); border: 1px solid rgb(220, 220, 220)">
                                                <tbody>
                                                    <tr>
                                                        <td valign="top">
                                                            <table border="0" cellpadding="0" cellspacing="0" width="600" style=" color: rgb(255, 255, 255); border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="padding: 10px; display: block">
                                                                          <h1 style="color: rgb(255, 255, 255); font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: center; text-shadow: 0 1px 0 rgb(119, 151, 180)"><img src="'.ITG_PLUGIN_URL.'/admin/images/ifthengive.png" alt="IfThenGive"></h1> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    <td valign="top">';                
        $EmailFooter = '</td></tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    </div>';
        $EmailString = '<div style="margin-right: -15px; margin-left: -15px;">
            <div style="width: 100%;">                
                <div style="margin: 10px 75px 25px; float: none; height: auto;font-weight: 600;">
                    <p style="padding: 10px;font-size: 12px;font-family: inherit; color: #076799">'. __('Hi '.$user->display_name.',',ITG_TEXT_DOMAIN).'
                    <br><br>'.__('You have been removed from the following goal:  '.$trigger_name,ITG_TEXT_DOMAIN).'
                    <br><br>                    
                    '.__('You will no longer be included in the donations that get processed with this goal occurs.',ITG_TEXT_DOMAIN).'
                    <br><br>
                    '.__('Thanks!',ITG_TEXT_DOMAIN).'
                    </p>
                    <p style="padding: 10px;font-size: 12px;font-family: inherit; color: #076799">'.get_bloginfo('name').'</p>
                </div>
            </div>
        </div>';
        
        $headers = "From: IfThenGive <info@ifthengive.com> \r\n";
        $headers .= "Reply-To: noreply@ifthengive.com \r\n";
        //$headers .= "CC: ifthengive@ifthengive.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $to = $admin_email = get_option('admin_email');
        $subject = __('IfThenGive Notification',ITG_TEXT_DOMAIN);
        $message = $EmailHeader.$EmailString.$EmailFooter;     
        wp_mail($to, $subject, $message, $headers);
        exit;
    }    
   
}

AngellEYE_IfThenGive_interface::init();
