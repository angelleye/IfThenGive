<?php
/**
 * Ifthengive Display Goal template.
 *
 * This template can be overriden by copying this file to your-theme/ifthengive/template-ifthengive-display-goal.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	IfThenGive
 * @version     0.1.0
 */
if (!defined('ABSPATH')) {
    exit; // Don't allow direct access
}
if (!empty($id)) {
    $post = get_post($id);    
    if (!empty($post->post_type) && $post->post_type == 'ifthengive_goals' && $post->post_status == 'publish') {
        do_action('before_ifthengive_goal', $id);
        $trigger_name = get_post_meta($post->ID, 'trigger_name', true);
        $image_url = get_post_meta($post->ID, 'image_url', true);
        $trigger_desc = get_post_meta($post->ID, 'trigger_desc', true);
        ?>
        
        <div class="overlay" style=" background: #d9d9da;opacity: 0.9;width: 100%;float: left;height: 100%;position: fixed;top: 0;left:0;right:0;z-index: 1031;text-align: center; display: none;">
            <div class="itg_loader"></div>
            <h1 style="font-weight: 600;"><?php esc_html_e('Processing...', 'ifthengive') ?></h1>
        </div>

        <div class="itg_container">                    
            <div class="itg_post-item">
                
                <?php do_action('before_ifthengive_goal_trigger_name', $id); ?>
                
                <div class="itg_post-title">
                    <h3><?php echo $trigger_name ?></h3>
                </div>
                
                <?php do_action('after_ifthengive_goal_trigger_name', $id); ?>
                
                <div class="itg_post-image">
                    <img src="<?php echo $image_url; ?>">
                </div>
                
                <?php do_action('after_ifthengive_goal_image', $id); ?>
                
                <div class="itg_post-content-details">
                    <div class="itg_post-description" id="scrolltopid_<?php echo $post->ID; ?>">
                        <p><?php echo $trigger_desc ?></p>
                    </div>
                    <?php
                    do_action('after_ifthengive_goal_desc', $id);
                    
                    $amount = get_post_meta($post->ID, 'amount', true);
                    $label =  sprintf('%1$s %2$s %3$s %4$s',
                                        esc_html__('If', 'ifthengive'),
                                        get_post_meta($post->ID, 'trigger_thing', true),
                                        esc_html__('Then I will Give', 'ifthengive'),
                                        $symbol
                                    );
                    if ($amount == 'fixed') {
                        ?>
                        <div class="itg_post-title">
                            <?php $fixed_amount = get_post_meta($post->ID, 'fixed_amount_input', true); ?>
                            <h4><?php echo $label;?><span id="ifthengive_fixed_price_span_<?php echo $post->ID; ?>"><?php echo $fixed_amount; ?></span></h4>
                        </div>
                        <?php
                    } elseif ($amount == 'manual') {
                        $manual_amount_input_value = get_post_meta($post->ID, 'manual_amount_input', true);
                        ?>
                        <div class="itg_post-title">
                            <h4><?php echo $label;?><span id="ifthengive_manual_price_span_<?php echo $post->ID; ?>"><?php echo $manual_amount_input_value; ?></span></h4>
                        </div>
                        <?php
                    } else {
                        $option_name = get_post_meta($post->ID, 'option_name', true);
                        $option_amount = get_post_meta($post->ID, 'option_amount', true);
                        $i = 0;
                        ?>
                        <div class="itg_post-title">
                            <h4><?php echo $label;?><span id="ifthengive_fixed_price_span_select_<?php echo $post->ID; ?>"><?php echo isset($option_amount[0]) ? $option_amount[0] : ''; ?></span></h4>
                        </div>
                        <?php
                    }
                    ?>
                </div> <!-- itg_post-content-details -->
            </div> <!-- itg_post-item -->

            <div class="itgcontainer" id="ifthengive_signup_form">
                <div class="itg_hr-title itg_center">
                    <abbr><?php echo sprintf('%1$s %2$s',esc_html__('Sign up for', 'ifthengive'),$trigger_name);?></abbr>
                </div>

                <div class="itg_alert itg_alert-warning" id="connect_paypal_error_public_<?php echo $post->ID; ?>" style="display: none">
                    <span id="connect_paypal_error_p_<?php echo $post->ID; ?>"></span>
                </div>

                <!--
                <p class="text-info"><?php //_e('Instructions', 'ifthengive'); ?>'</p>
                <ol>
                    <li>'.__('Lorem ipsum dolor sit amet.','ifthengive').'</li>
                    <li>'.__('Consectetur adipiscing elit.','ifthengive').'</li>
                    <li>'.__('Integer molestie lorem at massa.','ifthengive').'</li>
                </ol>
                -->
                <?php
                if (is_user_logged_in()) {
                    $current_user = wp_get_current_user();
                    $User_email = !empty($current_user->user_email) ? $current_user->user_email : '';
                    $User_first_name = !empty($current_user->user_firstname) ? $current_user->user_firstname : '';
                    $User_last_name = !empty($current_user->user_lastname) ? $current_user->user_lastname : '';
                    $user_id = !empty($current_user->ID) ? $current_user->ID : '';
                } else {
                    $User_email = '';
                    $User_first_name = '';
                    $User_last_name = '';
                    $user_id = '';
                }
                do_action('before_ifthengive_goal_signup_form', $id);
                ?>            
                <form method="post" name="signup" id="ifthengive_signup_<?php echo $post->ID; ?>">
                    <?php
                    if ($amount == 'fixed') {
                        
                    } elseif ($amount == 'manual') {
                        $manual_amount_input_value = get_post_meta($post->ID, 'manual_amount_input', true);
                        ?>
                        <div class="itg_form-group">
                            <label for="manualamout" class="itg_upper"><?php esc_html_e('Enter Amount', 'ifthengive'); ?></label>
                            <input type="text" 
                                   name="itg_manual_amount_input" 
                                   value="<?php echo $manual_amount_input_value; ?>"
                                   class="itg_form-control" 
                                   autocomplete="off" 
                                   id="itg_manual_amount_input_<?php echo $post->ID; ?>" 
                                   placeholder="Enter Amount"/>
                        </div>
                        <script>
                            jQuery(document).on("keyup", "#itg_manual_amount_input_<?php echo $post->ID; ?>", function () {
                                var amt = parseFloat(jQuery(this).val()).toFixed(2);
                                if (isNaN(amt)) {
                                    jQuery("#ifthengive_manual_price_span_<?php echo $post->ID; ?>").html("").html("1.00");
                                } else {
                                    jQuery("#ifthengive_manual_price_span_<?php echo $post->ID; ?>").html("").html(amt);
                                }
                            });
                            jQuery(document).on("input", "#itg_manual_amount_input_<?php echo $post->ID; ?>", function () {
                                this.value = this.value.replace(/[^0-9.]/g, "").replace(/(\..*)\./g, "$1");
                            });
                        </script>
                        <?php
                    } else {
                        $option_name = get_post_meta($post->ID, 'option_name', true);
                        $option_amount = get_post_meta($post->ID, 'option_amount', true);
                        $i = 0;
                        ?>
                        <div class="itg_form-group">
                            <select class="itg_form-control" name="ifthengive_option_amount" id="ifthengive_option_amount_<?php echo $post->ID; ?>">
                                <?php
                                foreach ($option_name as $name) {
                                    echo '<option value="' . $option_amount[$i] . '">' . $name . " " . $option_amount[$i] . '</option>';
                                    $i++;
                                }
                                ?>
                            </select>
                        </div>
                        <script>
                            jQuery(document).on("change", "#ifthengive_option_amount_<?php echo $post->ID; ?>", function () {
                                jQuery("#ifthengive_fixed_price_span_select_<?php echo $post->ID; ?>").html("").html(jQuery(this).val());
                            });
                        </script>
                        <?php
                    }
                    ?>

                    <div class="itg_form-group">
                        <label class="itg_upper" for="ifthengive_firstname_<?php echo $post->ID; ?>"><?php esc_html_e('First Name', 'ifthengive'); ?></label>
                        <input type="text" 
                               value="<?php echo $User_first_name; ?>"
                               class="itg_form-control"
                               name="ifthengive_firstname"
                               id="ifthengive_firstname_<?php echo $post->ID; ?>"
                               autocomplete="off"
                               required="required" />
                    </div>

                    <div class="itg_form-group">
                        <label class="itg_upper" for="ifthengive_lastname_<?php echo $post->ID; ?>"><?php esc_html_e('Last Name', 'ifthengive'); ?></label>
                        <input type="text"
                               class="itg_form-control"
                               name="ifthengive_lastname"
                               id="ifthengive_lastname_<?php echo $post->ID; ?>"
                               autocomplete="off"
                               required="required"
                               value="<?php echo $User_last_name; ?>" />
                    </div>

                    <div class="itg_form-group">
                        <label class="itg_upper" for="email"><?php esc_html_e('Email address', 'ifthengive'); ?></label>
                        <input type="email"
                               class="itg_form-control"
                               name="ifthengive_email"
                               id="ifthengive_email_<?php echo $post->ID; ?>"
                               autocomplete="off"
                               required="required"
                               value="<?php echo $User_email; ?>" />                                
                    </div>

        <?php if (!is_user_logged_in()) { ?>

                        <div class="checkbox">
                            <label class="itg_upper" for="itg_signup_as_guest_<?php echo $post->ID; ?>">
                                <input type="checkbox" 
                                       name="itg_signup_as_guest"
                                       id="itg_signup_as_guest_<?php echo $post->ID; ?>"
                                       checked />&nbsp;<?php esc_html_e('Create an account', 'ifthengive'); ?></label>
                        </div>

                        <div class="itg_form-group itg-password_'.$post->ID.'">
                            <label class="itg_upper" for="ifthengive_password_<?php echo $post->ID; ?>"><?php esc_html_e('Password', 'ifthengive'); ?></label>
                            <input type="password"
                                   class="itg_form-control"
                                   name="ifthengive_password"
                                   id="ifthengive_password_<?php echo $post->ID; ?>"
                                   required="required" />
                        </div>

                        <div class="itg_form-group itg-password_<?php echo $post->ID; ?>">
                            <label class="itg_upper" for="ifthengive_retype_password_<?php echo $post->ID; ?>"><?php esc_html_e('Re-type Password', 'ifthengive'); ?></label>
                            <input type="password"
                                   class="itg_form-control"
                                   name="ifthengive_retype_password"
                                   id="ifthengive_retype_password_<?php echo $post->ID; ?>"
                                   required="required" />
                        </div>

                        <script>
                            jQuery(document).on("click", "#itg_signup_as_guest_<?php echo $post->ID; ?>", function () {
                                if (jQuery(this).is(":checked")) {
                                    jQuery(".itg-password_<?php echo $post->ID; ?>").show(300);
                                } else {
                                    jQuery(".itg-password_<?php echo $post->ID; ?>").hide(200);
                                }
                            });
                        </script>
                        <?php
                    }
                    ?>
                    <input type="hidden" name="ifthengive_page_id" id="ifthengive_page_id_<?php echo $post->ID; ?>" value="<?php echo $current_url; ?>" />
                        <?php wp_nonce_field('itg_goal_form', '_itg_goal_form_nonce'); ?>
                    <button type="button" class="itg_btn itg_btn-primary ifthengive_angelleye_checkout" data-postid="<?php echo $post->ID; ?>" data-userid="<?php echo $user_id; ?>">
                        <?php echo sprintf('%1$s %2$s',esc_html('Sign Up For', 'ifthengive'),$trigger_name);?>
                    </button>
                </form>
                <?php do_action('after_ifthengive_goal_signup_form', $id); ?>
            </div> <!-- itgcontainer -->
        </div> <!-- itg_container -->
        <?php
        do_action('after_ifthengive_goal', $id);
    }
}