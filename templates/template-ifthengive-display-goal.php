<?php
/**
 * Ifthengive Display Goal template.
 *
 * This template can be overriden by copying this file to your-theme/......php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	IfThenGive
 * @version     1.0.0
 */
if (!defined('ABSPATH')){
    exit; // Don't allow direct access
}            
        global $post, $post_ID , $wp;
        $current_url =  home_url( $wp->request ); 
        $ifthengive_page_id = $current_url;       
        $html = '';
        $ccode = get_option('itg_currency_code');
        $paypal = new AngellEYE_IfThenGive_PayPal_Helper();
        $symbol = $paypal->get_currency_symbol($ccode);
        if( !empty($id) ) {
            $post = get_post($id);
            if(!empty($post->post_type) && $post->post_type == 'ifthengive_goals' && $post->post_status == 'publish') {
        
                $html .= '<div class="overlay" style=" background: #d9d9da;opacity: 0.9;width: 100%;float: left;height: 100%;position: fixed;top: 0;left:0;right:0;z-index: 1031;text-align: center; display: none;">';
                $html .=  '<div class="itg_loader"></div>
                           <h1 style="font-weight: 600;">'.esc_html('Processing...','ifthengive').'</h1></div>';                
                $html .= '<div class="itg_container">';
                    $html .= '<div class="itg_post-item">';                           
                            
                            $html .= '<div class="itg_post-title">
                                        <h3>'.get_post_meta( $post->ID, 'trigger_name', true ).'</h3>
                                      </div>';
                            
                            $html .= '<div class="itg_post-image">';
                            $html .= '<img src="'.get_post_meta( $post->ID, 'image_url', true ).'">';
                            $html .= '</div>';

                            $html .= '<div class="itg_post-content-details">'; 
                                $html .= '<div class="itg_post-description" id="scrolltopid_'.$post->ID.'">
                                            <p>'.get_post_meta( $post->ID, 'trigger_desc', true ).'</p>
                                          </div>';
                                $html .= $post->post_content;                                                                                
                                $amount = get_post_meta($post->ID,'amount',true);

                                if($amount == 'fixed'){
                                    $html .= '<div class="itg_post-title">';
                                    $fixed_amount = get_post_meta($post->ID,'fixed_amount_input',true);                                
                                    $html .= '<h4>'.esc_html('If ','ifthengive').get_post_meta( $post->ID, 'trigger_thing', true ). esc_html(' Then I will Give ','ifthengive').$symbol.'<span id="ifthengive_fixed_price_span_'.$post->ID.'">'.$fixed_amount.'</span></h4>';
                                    $html .= '</div>';                                    
                                }                                
                                elseif($amount == 'manual'){
                                    $manual_amount_input_value = get_post_meta($post->ID, 'manual_amount_input', true);
                                    $html .= '<div class="itg_post-title">';
                                        $html .= '<h4>'.esc_html('If ','ifthengive').get_post_meta( $post->ID, 'trigger_thing', true ).esc_html(' Then I will Give ','ifthengive').$symbol.'<span id="ifthengive_manual_price_span_'.$post->ID.'">'.$manual_amount_input_value.'</span></h4>';
                                    $html .= '</div>';                                   
                                }
                                else{
                                    $option_name = get_post_meta($post->ID,'option_name',true);
                                    $option_amount = get_post_meta($post->ID,'option_amount',true);                                    
                                    $i=0;
                                    $html .= '<div class="itg_post-title">';
                                        $html .= '<h4>'.esc_html('If ','ifthengive').get_post_meta( $post->ID, 'trigger_thing', true ).esc_html(' Then I will Give ','ifthengive').$symbol.'<span id="ifthengive_fixed_price_span_select_'.$post->ID.'">'.$option_amount[0].'</span></h4>';
                                    $html .= '</div>';                                   
                                }
                        $html .= '</div>'; // itg_post-content-details
                    $html .= '</div>'; // itg_post-item 
                                       
                    $html .= '<div class="itgcontainer" id="ifthengive_signup_form">';                 
                        $html .= '<div class="itg_hr-title itg_center">';
                        $html .= '<abbr>'.esc_html('Sign up for ','ifthengive'). get_post_meta( $post->ID, 'trigger_name', true ).'</abbr>';
                        $html .= '</div>';
                                                             
                        $html .= '<div class="itg_alert itg_alert-warning" id="connect_paypal_error_public_'.$post->ID.'" style="display: none">';
                        $html .= '<span id="connect_paypal_error_p_'.$post->ID.'"></span>';
                        $html .= '</div>';
                        
/*                        $html .= '<p class="text-info">'.__('Instructions','ifthengive').'</p>';
                        $html .='<ol>
                                    <li>'.__('Lorem ipsum dolor sit amet.','ifthengive').'</li>
                                    <li>'.__('Consectetur adipiscing elit.','ifthengive').'</li>
                                    <li>'.__('Integer molestie lorem at massa.','ifthengive').'</li>
                                </ol>';*/
                                    
                                     if ( is_user_logged_in() ) {
                                        $current_user    = wp_get_current_user();
                                        $User_email      = !empty($current_user->user_email) ? $current_user->user_email : '';
                                        $User_first_name = !empty($current_user->user_firstname) ? $current_user->user_firstname : '';
                                        $User_last_name  = !empty($current_user->user_lastname) ? $current_user->user_lastname : '';
                                        $user_id = !empty($current_user->ID) ? $current_user->ID : '';
                                     }
                                     else{
                                        $User_email      = '';
                                        $User_first_name = '';
                                        $User_last_name  = '';
                                        $user_id = '';
                                     }
                                    
                                    $html .= '<form method="post" name="signup" id="ifthengive_signup_'.$post->ID.'">';                                        
                                        if($amount == 'fixed'){                                            
                                        }                                
                                        elseif($amount == 'manual'){
                                            $manual_amount_input_value = get_post_meta($post->ID, 'manual_amount_input', true);                                            
                                            $html .= '<div class="itg_form-group">';
                                                $html .= '<label for="manualamout" class="itg_upper">'. esc_html('Enter Amount','ifthengive').'</label>';
                                                $html .= '<input type="text" name="itg_manual_amount_input" value="'.$manual_amount_input_value.'" class="itg_form-control" autocomplete="off" id="itg_manual_amount_input_'.$post->ID.'" placeholder="Enter Amount"/>';
                                            $html .= '</div>';
                                            $html .= '<script>
                                                        jQuery(document).on("keyup","#itg_manual_amount_input_'.$post->ID.'", function (){
                                                            var amt = parseFloat(jQuery(this).val()).toFixed(2);
                                                            if(isNaN(amt)){
                                                                jQuery("#ifthengive_manual_price_span_'.$post->ID.'").html("").html("1.00");
                                                            }else{
                                                                jQuery("#ifthengive_manual_price_span_'.$post->ID.'").html("").html(amt);
                                                            }
                                                        });
                                                        jQuery(document).on("input","#itg_manual_amount_input_'.$post->ID.'", function() {
                                                            this.value = this.value.replace(/[^0-9.]/g, "").replace(/(\..*)\./g, "$1");                                                            
                                                        });
                                                    </script>';
                                        }
                                        else{
                                            $option_name = get_post_meta($post->ID,'option_name',true);
                                            $option_amount = get_post_meta($post->ID,'option_amount',true);
                                            $i=0;                                           
                                            $html .= '<div class="itg_form-group">';
                                                $html .= '<select class="itg_form-control" name="ifthengive_option_amount" id="ifthengive_option_amount_'.$post->ID.'">';

                                                foreach ($option_name as $name) {
                                                     $html .=  '<option value="'.$option_amount[$i].'">'.$name." ".$option_amount[$i].'</option>';                                        
                                                $i++;
                                                }
                                                $html .= '</select>';
                                            $html .= '</div>';
                                            $html .= '<script>
                                                        jQuery(document).on("change","#ifthengive_option_amount_'.$post->ID.'", function (){
                                                            jQuery("#ifthengive_fixed_price_span_select_'.$post->ID.'").html("").html(jQuery(this).val());
                                                        });
                                                        </script>';
                                        }
                                        $html .= '<div class="itg_form-group">';                                        
                                          $html .= '<label class="itg_upper" for="name">'.esc_html('First Name','ifthengive').'</label>';
                                          $html .= '<input type="text" class="itg_form-control" name="ifthengive_firstname" id="ifthengive_firstname_'.$post->ID.'" autocomplete="off" required="required" value="'.$User_first_name.'">';
                                        $html .= '</div>';
                                        $html .= '<div class="itg_form-group">';
                                          $html .= '<label class="itg_upper" for="name">'.esc_html('Last Name','ifthengive').'</label>';
                                          $html .= '<input type="text" class="itg_form-control" name="ifthengive_lastname" id="ifthengive_lastname_'.$post->ID.'" autocomplete="off" required="required" value="'. $User_last_name.'">';
                                        $html .= '</div>';
                                        $html .= '<div class="itg_form-group">';
                                          $html .= '<label class="itg_upper" for="email">'. esc_html('Email address','ifthengive').'</label>';
                                          $html .= '<input type="email" class="itg_form-control" name="ifthengive_email" id="ifthengive_email_'.$post->ID.'" autocomplete="off" required="required" value="'.$User_email.'">';
                                        $html .= '</div>';                                                                                                                    
                                         if ( ! is_user_logged_in() ) {
                                        $html .=  '<div class="checkbox">';
                                        $html .=    '<label class="itg_upper">';
                                        $html .=      '<input type="checkbox" name="itg_signup_as_guest" id="itg_signup_as_guest_'.$post->ID.'" checked>'.esc_html('Create an account','ifthengive');
                                        $html .=    '</label>';
                                        $html .=  '</div><br>';
                                        $html .= '<div class="itg_form-group itg-password_'.$post->ID.'">';
                                          $html .= '<label class="itg_upper" for="password">'.esc_html('Password','ifthengive').'</label>';
                                          $html .= '<input type="password" class="itg_form-control" name="ifthengive_password" id="ifthengive_password_'.$post->ID.'" required="required">';
                                        $html .= '</div>';
                                        $html .= '<div class="itg_form-group itg-password_'.$post->ID.'">';
                                          $html .= '<label class="itg_upper" for="password">'.esc_html('Re-type Password','ifthengive').'</label>';
                                          $html .= '<input type="password" class="itg_form-control" name="ifthengive_retype_password" id="ifthengive_retype_password_'.$post->ID.'" required="required">';
                                        $html .= '</div>';
                                        $html .= '<script>
                                                    jQuery(document).on("click","#itg_signup_as_guest_'.$post->ID.'",function() {
                                                        if(jQuery(this).is(":checked")) {
                                                            jQuery(".itg-password_'.$post->ID.'").show(300);
                                                        } else {
                                                            jQuery(".itg-password_'.$post->ID.'").hide(200);
                                                        }
                                                    });
                                                  </script>';
                                         }                                        
                                        $html .= '<input type="hidden" name="ifthengive_page_id" id="ifthengive_page_id_'.$post->ID.'" value="'.$ifthengive_page_id.'">';
                                        $html .= wp_nonce_field('itg_goal_form','_itg_goal_form_nonce');
                                        $html .= '<button type="button" class="itg_btn itg_btn-primary ifthengive_angelleye_checkout" data-postid="'.$post->ID.'" data-userid="'.$user_id.'">'.esc_html('Sign Up For ','ifthengive') . get_post_meta( $post->ID, 'trigger_name', true ).'</button>';
                                    $html .= '</form>';
                                $html .= '</div>'; // itgcontainer
                            $html .= '</div>'; // itg_container                                     
                            
            }
        }
        echo $html;
?>