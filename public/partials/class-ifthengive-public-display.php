<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 *
 * @package    IfThenGive
 * @subpackage IfThenGive/public/partials
 */
class AngellEYE_IfThenGive_Public_Display {

    public static function init() {
        add_shortcode('ifthengive_goal', array(__CLASS__, 'ifthengive_create_shortcode'));
        /*add_action( 'wp_enqueue_scripts', array(__CLASS__,'ifthengive_detect_shortcode'));*/
        add_action( 'wp_ajax_start_express_checkout', array(__CLASS__,'start_express_checkout'));
        add_action("wp_ajax_nopriv_start_express_checkout",  array(__CLASS__,'start_express_checkout'));
        add_action( 'wp_ajax_ifthengive_my_transactions', array(__CLASS__,'ifthengive_my_transactions'));
        add_action("wp_ajax_nopriv_ifthengive_my_transactions",  array(__CLASS__,'ifthengive_my_transactions'));
        
        add_action( 'wp_ajax_ifthengive_my_goals', array(__CLASS__,'ifthengive_my_goals'));
        add_action("wp_ajax_nopriv_ifthengive_my_goals",  array(__CLASS__,'ifthengive_my_goals'));   
        
        add_action( 'wp_ajax_cancel_my_account_ba', array(__CLASS__,'cancel_my_account_ba'));
        add_action("wp_ajax_nopriv_cancel_my_account_ba",  array(__CLASS__,'cancel_my_account_ba'));
        
        add_action( 'wp_ajax_itg_adjust_amount', array(__CLASS__,'itg_adjust_amount'));
        add_action("wp_ajax_nopriv_itg_adjust_amount",  array(__CLASS__,'itg_adjust_amount'));  
        
        add_action( 'wp_ajax_change_giver_status', array(__CLASS__,'change_giver_status'));
        add_action("wp_ajax_nopriv_change_giver_status",  array(__CLASS__,'change_giver_status'));          
        
        add_action( 'wp_enqueue_scripts', array(__CLASS__,'ifthengive_detect_my_account_shortcode'));
    }
    
    
    public static function ifthengive_detect_my_account_shortcode()
    {
        global $post;
        if($post){
            if(has_shortcode( $post->post_content, 'ifthengive_account')){
                wp_enqueue_script('ifthengive_plugin_compress', ITG_PLUGIN_URL . 'public/js/plugins-compressed.js', array('jquery'));
            }
        }               
    }
   
    /*
     * ifthengive_detect_shortcode function is added only to detect the ifthengive shortcode in the page content.
     * It was neccesory when you have to load particular CSS or JS only when shotcode detect.
     * we have Bootstrap design before, but now we have our own cutom class so it will not affect/conflict with
     * other css class.
     *  
    public static function ifthengive_detect_shortcode()
    {
        global $post;
        $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches )
            && in_array( 'ifthengive_goal', $matches[2] ) )
        {            
            //wp_enqueue_style( 'ifthengive-one', ITG_PLUGIN_URL . 'includes/css/bootstrap/css/bootstrap.css', array(), '1.0.0','all' );
        }
    }*/
          
    /**
     * ifthengive_create_shortcode function is for generate
     * @since 1.0.0
     * @access public
     */
    public static function ifthengive_create_shortcode($atts, $content = null) {
        global $post, $post_ID , $wp;        
        $current_url =  home_url( $wp->request ); 
        $ifthengive_page_id = $current_url;
        extract(shortcode_atts(array(
                    'id' => ''), $atts));
        $html = '';
        $ccode = get_option('itg_currency_code');
        $paypal = new AngellEYE_IfThenGive_PayPal_Helper();
        $symbol = $paypal->get_currency_symbol($ccode);
        if( !empty($id) ) {
            $post = get_post($id);
            if(!empty($post->post_type) && $post->post_type == 'ifthengive_goals' && $post->post_status == 'publish') {
        
                $html .= '<div id="overlay" style=" background: #d9d9da;opacity: 0.9;width: 100%;float: left;height: 100%;position: fixed;top: 0;left:0;right:0;z-index: 1031;text-align: center; display: none;">';
                $html .=  '<div class="itg_loader"></div>
                           <h1 style="font-weight: 600;">'.esc_html('Processing...',ITG_TEXT_DOMAIN).'</h1></div>';                
                $html .= '<div class="itg_container">';
                    $html .= '<div class="itg_post-item">';                           
                            
                            $html .= '<div class="itg_post-title">
                                        <h3>'.get_post_meta( $post->ID, 'trigger_name', true ).'</h3>
                                      </div>';
                            
                            $html .= '<div class="itg_post-image">';
                            $html .= '<img src="'.get_post_meta( $post->ID, 'image_url', true ).'">';
                            $html .= '</div>';

                            $html .= '<div class="itg_post-content-details">'; 
                                $html .= '<div class="itg_post-description" id="scrolltopid">
                                            <p>'.get_post_meta( $post->ID, 'trigger_desc', true ).'</p>
                                          </div>';
                                $html .= $post->post_content;                                                                                
                                $amount = get_post_meta($post->ID,'amount',true);

                                if($amount == 'fixed'){
                                    $html .= '<div class="itg_post-title">';
                                    $fixed_amount = get_post_meta($post->ID,'fixed_amount_input',true);                                
                                    $html .= '<h4>'.esc_html('If ',ITG_TEXT_DOMAIN).'&nbsp;'.get_post_meta( $post->ID, 'trigger_thing', true ). esc_html(' Then I will Give ',ITG_TEXT_DOMAIN).$symbol.'<span id="ifthengive_fixed_price_span">'.number_format($fixed_amount,2).'</span></h4>';
                                    $html .= '</div>';                                    
                                }                                
                                elseif($amount == 'manual'){
                                    $manual_amount_input_value = get_post_meta($post->ID, 'manual_amount_input', true);
                                    $html .= '<div class="itg_post-title">';
                                        $html .= '<h4>'.esc_html('If ',ITG_TEXT_DOMAIN).'&nbsp;'.get_post_meta( $post->ID, 'trigger_thing', true ).esc_html(' Then I will Give ',ITG_TEXT_DOMAIN).$symbol.'<span id="ifthengive_manual_price_span">'.$manual_amount_input_value.'</span></h4>';
                                    $html .= '</div>';                                   
                                }
                                else{
                                    $option_name = get_post_meta($post->ID,'option_name',true);
                                    $option_amount = get_post_meta($post->ID,'option_amount',true);
                                    $i=0;
                                    $html .= '<div class="itg_post-title">';
                                        $html .= '<h4>'.esc_html('If ',ITG_TEXT_DOMAIN).'&nbsp;'.get_post_meta( $post->ID, 'trigger_name', true ).esc_html(' Then I will Give ',ITG_TEXT_DOMAIN).$symbol.'<span id="ifthengive_fixed_price_span_select">'.number_format($option_amount[0],2).'</span></h4>';
                                    $html .= '</div>';                                   
                                }
                        $html .= '</div>'; // itg_post-content-details
                    $html .= '</div>'; // itg_post-item 
                                       
                    $html .= '<div class="itgcontainer" id="ifthengive_signup_form">';                 
                        $html .= '<div class="itg_hr-title itg_center">';
                        $html .= '<abbr>'.esc_html('Sign up for ',ITG_TEXT_DOMAIN). get_post_meta( $post->ID, 'trigger_name', true ).'</abbr>';
                        $html .= '</div>';
                                                             
                        $html .= '<div class="itg_alert itg_alert-warning" id="connect_paypal_error_public" style="display: none">';
                        $html .= '<span id="connect_paypal_error_p"></span>';
                        $html .= '</div>';
                        
/*                        $html .= '<p class="text-info">'.__('Instructions',ITG_TEXT_DOMAIN).'</p>';
                        $html .='<ol>
                                    <li>'.__('Lorem ipsum dolor sit amet.',ITG_TEXT_DOMAIN).'</li>
                                    <li>'.__('Consectetur adipiscing elit.',ITG_TEXT_DOMAIN).'</li>
                                    <li>'.__('Integer molestie lorem at massa.',ITG_TEXT_DOMAIN).'</li>
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
                                    
                                    $html .= '<form method="post" name="signup" id="ifthengive_signup">';                                        
                                        if($amount == 'fixed'){                                            
                                        }                                
                                        elseif($amount == 'manual'){
                                            $manual_amount_input_value = get_post_meta($post->ID, 'manual_amount_input', true);                                            
                                            $html .= '<div class="itg_form-group">';
                                                $html .= '<label for="manualamout" class="itg_upper">'. esc_html('Enter Amount',ITG_TEXT_DOMAIN).'</label>';
                                                $html .= '<input type="text" name="itg_manual_amount_input" value="'.$manual_amount_input_value.'" class="itg_form-control" autocomplete="off" id="itg_manual_amount_input" placeholder="Enter Amount"/>';
                                            $html .= '</div>';
                                        }
                                        else{
                                            $option_name = get_post_meta($post->ID,'option_name',true);
                                            $option_amount = get_post_meta($post->ID,'option_amount',true);
                                            $i=0;                                           
                                            $html .= '<div class="itg_form-group">';
                                                $html .= '<select class="itg_form-control" name="ifthengive_option_amount" id="ifthengive_option_amount">';

                                                foreach ($option_name as $name) {
                                                     $html .=  '<option value="'.number_format($option_amount[$i],2).'">'.$name." ".number_format($option_amount[$i],2).'</option>';                                        
                                                $i++;
                                                }
                                                $html .= '</select>';
                                            $html .= '</div>';
                                        }
                                        $html .= '<div class="itg_form-group">';                                        
                                          $html .= '<label class="itg_upper" for="name">'.esc_html('First Name',ITG_TEXT_DOMAIN).'</label>';
                                          $html .= '<input type="text" class="itg_form-control" name="ifthengive_firstname" id="ifthengive_firstname" autocomplete="off" required="required" value="'.$User_first_name.'">';
                                        $html .= '</div>';
                                        $html .= '<div class="itg_form-group">';
                                          $html .= '<label class="itg_upper" for="name">'.esc_html('Last Name',ITG_TEXT_DOMAIN).'</label>';
                                          $html .= '<input type="text" class="itg_form-control" name="ifthengive_lastname" id="ifthengive_lastname" autocomplete="off" required="required" value="'. $User_last_name.'">';
                                        $html .= '</div>';
                                        $html .= '<div class="itg_form-group">';
                                          $html .= '<label class="itg_upper" for="email">'. esc_html('Email address',ITG_TEXT_DOMAIN).'</label>';
                                          $html .= '<input type="email" class="itg_form-control" name="ifthengive_email" id="ifthengive_email" autocomplete="off" required="required" value="'.$User_email.'">';
                                        $html .= '</div>';                                                                                                                    
                                         if ( ! is_user_logged_in() ) {
                                        $html .=  '<div class="checkbox">';
                                        $html .=    '<label class="itg_upper">';
                                        $html .=      '<input type="checkbox" name="itg_signup_as_guest" id="itg_signup_as_guest" checked>'.esc_html('Create an account',ITG_TEXT_DOMAIN);
                                        $html .=    '</label>';
                                        $html .=  '</div><br>';
                                        $html .= '<div class="itg_form-group itg-password">';
                                          $html .= '<label class="itg_upper" for="password">'.esc_html('Password',ITG_TEXT_DOMAIN).'</label>';
                                          $html .= '<input type="password" class="itg_form-control" name="ifthengive_password" id="ifthengive_password" required="required">';
                                        $html .= '</div>';
                                        $html .= '<div class="itg_form-group itg-password">';
                                          $html .= '<label class="itg_upper" for="password">'.esc_html('Re-type Password',ITG_TEXT_DOMAIN).'</label>';
                                          $html .= '<input type="password" class="itg_form-control" name="ifthengive_retype_password" id="ifthengive_retype_password" required="required">';
                                        $html .= '</div>';
                                         }                                        
                                        $html .= '<input type="hidden" name="ifthengive_page_id" id="ifthengive_page_id" value="'.$ifthengive_page_id.'">';
                                        $html .= '<button type="button" class="itg_btn itg_btn-primary" id="ifthengive_angelleye_checkout" data-postid="'.$post->ID.'" data-userid="'.$user_id.'">'.esc_html('Sign Up For ',ITG_TEXT_DOMAIN) . get_post_meta( $post->ID, 'trigger_name', true ).'</button>';
                                    $html .= '</form>';
                                $html .= '</div>'; // itgcontainer
                            $html .= '</div>'; // itg_container                        
            }
        }
        return $html;        
    }
         
    public function start_express_checkout(){        
        global $wpdb;
        /*Getting data from ajax */        
        $post_id = $_POST['post_id'];
        $amount = number_format($_POST['amount'],2);        
        
        /* Get user information  from Form Data. */
        $itguser = array();
        parse_str($_POST['formData'], $itguser);
        
        /*valodation starts */
        $ValidationErrors = array();
        $fname = sanitize_text_field( $itguser['ifthengive_firstname']);
        if (!preg_match("/^[a-zA-Z]+$/",$fname)) {
          $ValidationErrors['FirstName'] = __("Invalid Input : Only letters allowed in First Name",ITG_TEXT_DOMAIN);
        }
        $lname = sanitize_text_field($itguser['ifthengive_lastname']);
        if (!preg_match("/^[a-zA-Z]+$/",$lname)) {
          $ValidationErrors['LastName'] = __("Invalid Input : Only letters allowed in Last Name",ITG_TEXT_DOMAIN);
        }

        $email = $itguser['ifthengive_email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $ValidationErrors['Email'] = __("Invalid email format",ITG_TEXT_DOMAIN);
        }
        if(isset($itguser['ifthengive_password'])){
            if ($itguser['ifthengive_password'] !== $itguser['ifthengive_retype_password']) {
                $ValidationErrors['Password'] = __("Mismatch Input : Password Fields are not matched",ITG_TEXT_DOMAIN);
            }
        }                                
        if(!empty($ValidationErrors)){
            echo json_encode(array('Ack'=>__('ValidationError',ITG_TEXT_DOMAIN),'ErrorCode'=>__('Invalid Inputs',ITG_TEXT_DOMAIN),'ErrorLong'=>__('Please find Following Error',ITG_TEXT_DOMAIN),'Errors'=>$ValidationErrors));
            exit;
        }            
        /*valodation End */            
            
        /*Get trigger_name of Post */        
        $trigger_name = get_post_meta( $post_id, 'trigger_name', true );       
        
        /*Create cancel page url like return to the cancel page from where it goes.*/        
        $cancel_page = $itguser['ifthengive_page_id'];
            
        /*if no role defined in the code then it adds new role as giver */
        $role = get_role( 'giver' );
        if($role==NULL){
            add_role('giver','Giver');
        }
        /*Create array of user data */
        $userdata=array(
                'user_pass' => isset($itguser['ifthengive_password']) ? $itguser['ifthengive_password'] : '',
                'user_login' => isset($itguser['ifthengive_email']) ? $itguser['ifthengive_email'] : '',
                'user_email' => isset($itguser['ifthengive_email']) ? $itguser['ifthengive_email'] : '',
                'display_name' => $itguser['ifthengive_firstname'].' '.$itguser['ifthengive_lastname'],
                'first_name' => isset($itguser['ifthengive_firstname']) ? $itguser['ifthengive_firstname'] : '',
                'last_name' => isset($itguser['ifthengive_lastname']) ? $itguser['ifthengive_lastname'] : '',
                'role' => 'giver'
        );
        /*
         * Below code for situations like
         * 1). If user is login and inserts diffrent email then he has on WP user list we will remain login user id.
         * 2). user is not login and directly enter email that we stored in usermeta.                    
         */
        $external_email_userid='';
//        $records = $wpdb->get_row( "SELECT user_id FROM ".$wpdb->prefix."usermeta WHERE `meta_key` IN ('itg_gec_email','itg_external_email') AND `meta_value`='".$itguser['ifthengive_email']."' LIMIT 1", ARRAY_A );
//        if($records){
//           $external_email_userid=$records['user_id'];
//        }
//        else{
//            /*Nothing match in database*/
//            //echo 'here';
//        }
        
        $user_exist = email_exists($itguser['ifthengive_email']);
        /*If user exist then just add capabilities of giver with current capabilities. */
        if($user_exist){
            unset($userdata['user_pass']);
            /*if user is admin then no change in the role*/
            $is_admin = user_can($user_exist, 'manage_options' );
            if($is_admin){
                unset($userdata['role']);
            }else{
                /* if user is not admin then add additional role to the current user */
                $theUser = new WP_User($user_exist);
                $theUser->add_role( 'giver' );
                unset($userdata['role']);
            }                
            $userdata['ID'] = $user_exist;        
            $user_id =$user_exist;
        }
        else{            
            /* 1) email not exist. i.e. user is login but enterd new email. 
             * 2) email not exist and user is not login but Email is already in metakey
             * first of condition checks if user login then get user id from post
             * if not login then check for external user id
             * if both empty then it will go further
             */
            if(is_user_logged_in()){
                $user_id = $_POST['login_user_id'];
            }
            else{
                $user_id = $external_email_userid;
            }
            if(!empty($user_id)){
                $userdata['ID'] = $user_id;            
                unset($userdata['user_pass']);
                unset($userdata['user_login']);
                unset($userdata['user_email']);
                unset($userdata['display_name']);
                unset($userdata['first_name']);
                unset($userdata['last_name']);

                $theUser = new WP_User($user_id);            
                $userdata['user_email'] = $theUser->data->user_email;
                $userdata['user_nicename'] = $theUser->data->user_nicename;
                $userdata['user_login'] = $theUser->data->user_login;
                $userdata['display_name'] = $theUser->data->display_name;
                $userdata['first_name'] = $theUser->data->first_name;
                $userdata['last_name'] = $theUser->data->last_name;

                /*if user is admin then no change in the role*/
                $is_admin = user_can($user_id, 'manage_options' );            
                if($is_admin){
                    unset($userdata['role']);
                }else{
                    /* if user is not admin then add additional role to the current user */                
                    $theUser->add_role( 'giver' );                
                    unset($userdata['role']);
                } 
                $itguser['itg_signup_as_guest']='on';
                //echo "<br>user not exist. i.e. Always new user to signup in goal but user is login<br>";
                //update_user_meta($user_id,'itg_external_email', $itguser['ifthengive_email']);
            }            
        }        
        
        if(!empty($user_id)){
            // User login
            
            /*Check if user have already a Billing Agreement then add just signedup for that goal and get it back with info */
            $isAvailableBAID = get_user_meta($user_id,'itg_gec_billing_agreement_id',true);        
            if(!empty($isAvailableBAID)){
                /*Check if user is already signed up for this goal then get him back with info.*/
                $signnedup_goals = get_user_meta($user_exist,'itg_signedup_goals');        
                $goalArray = explode('|', $signnedup_goals[0]);                
                if(!empty($goalArray)){
                    if(in_array($post_id, $goalArray)){
                        echo json_encode(array('Ack'=>__('Information',ITG_TEXT_DOMAIN),'ErrorCode'=>__('001',ITG_TEXT_DOMAIN),'ErrorShort'=>__('You are already signed up for this goal.',ITG_TEXT_DOMAIN),'ErrorLong'=>__('We already have a record of this email address signed up for this goal.',ITG_TEXT_DOMAIN)));
                        exit;
                    }
                }
                $sanbox_enable = get_option('itg_sandbox_enable');
                $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
                /* Create new post for signup post type and save goal_id,user_id,amount */
                $new_post_id = wp_insert_post( array(
                    'post_author' => $user_id,
                    'post_status' => 'publish',
                    'post_type' => 'itg_sign_up',
                    'post_title' => ('User ID : '.$user_id.'& Goal ID : '.$post_id)
                ) );

                update_post_meta($new_post_id,'itg_signup_amount',$amount);                    
                update_post_meta($new_post_id,'itg_signup_wp_user_id',$user_id);
                update_post_meta($new_post_id,'itg_signup_wp_goal_id',$post_id);
                update_post_meta($new_post_id, 'itg_signup_in_sandbox', $sandbox);
                update_post_meta($new_post_id,'itg_transaction_status','0');
                
                $amount = base64_encode($amount);
                $post = get_post($post_id); 
                $slug = $post->post_name;
                $urlusr = base64_encode($user_id);
                $REDIRECTURL = site_url('itg-thankyou?goal='.$slug.'&amt='.$amount.'&user='.$urlusr);                
                /* Add post id in the user's signedup goals */
                $signedup_goals= get_user_meta($user_id,'itg_signedup_goals',true);
                if($signedup_goals !=''){
                $signedup_goals = $signedup_goals."|".$post_id;
                }
                else{
                    $signedup_goals = $post_id;
                }        
                wp_set_auth_cookie( $user_id, true );
                update_user_meta($user_id,'itg_signedup_goals',$signedup_goals);
                update_user_meta($user_id,'itg_giver_'.$post_id.'_status','active');                                
                echo json_encode(array('Ack'=>'Success','RedirectURL'=>$REDIRECTURL));
                exit;
            }
        }
        else{            
            // User not login I.e. Always new user            
            //echo "<br>i am here User not login I.e. Always new user";
        }       
        /*Save user data in Session. */
        if(!session_id()) {
                session_start();
            }            
        
        if(isset($itguser['itg_signup_as_guest']) && $itguser['itg_signup_as_guest']=='on' ){
            $_SESSION['itg_guest_user'] = 'no';            
        }
        else{
            $_SESSION['itg_guest_user'] = 'yes';
            if(empty($user_id)){
                $userdata['user_pass'] = 'ITGPassword';
            }
        }        
        $_SESSION['itg_user_data'] = $userdata;  
        
        $_SESSION['itg_signup_amount'] = $amount;
        $_SESSION['itg_signup_wp_user_id'] = $user_id;
        $_SESSION['itg_signup_wp_goal_id'] = $post_id;
                
        $brandname = get_option('itg_brandname');
        $logoimg = get_option('itg_brandlogo');
        $hdlogoimg = get_option('itg_hd_brandlogo');
        $customerservicenumber = get_option('itg_cs_number');
        /*PayPal setup */                
        $PayPal_config = new AngellEYE_IfThenGive_PayPal_Helper();
        $PayPal_config->set_api_cedentials();
        $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
        /*
         *   By default Angell EYE PayPal PHP Library has ButtonSource is "AngellEYE_PHPClass".
         *   We are overwirting that variable with "AngellEYE_IfThenGive" value.
         *   It also reflactes in NVPCredentials string so we are also replcing it.
         */
        $PayPal->APIButtonSource = ITG_BUTTON_SOURCE;
        $PayPal->NVPCredentials = str_replace('AngellEYE_PHPClass',ITG_BUTTON_SOURCE,$PayPal->NVPCredentials);        
        $SECFields = array(
                'maxamt' => round($amount * 2,2),
                'returnurl' => site_url('?action=ec_return'),
                'cancelurl' => $cancel_page,
                'hdrimg' => isset($hdlogoimg) ? $hdlogoimg : '',
                'logoimg' => isset($logoimg) ? $logoimg : '',
                'brandname' => (isset($brandname) && !empty($brandname)) ? $brandname : get_bloginfo('name'),
                'customerservicenumber' => isset($customerservicenumber) ? $customerservicenumber : '',
        );
        $Payments = array();
        $Payment = array(
            'amt' => 0
        );
        array_push($Payments, $Payment);
        
        $BillingAgreements = array();
        $Item = array(
                'l_billingtype' => apply_filters('itg_ec_billingtype','MerchantInitiatedBilling'),
                'l_billingagreementdescription' => $trigger_name,
                'l_paymenttype' => '',
                'l_billingagreementcustom' => 'ifthengive_'.$post_id
        );
        array_push($BillingAgreements, $Item);

        $PayPalRequestData = array(
            'SECFields' => $SECFields, 
            'Payments' => $Payments,
            'BillingAgreements' => $BillingAgreements,
        );
        $PayPalResult = $PayPal->SetExpressCheckout($PayPalRequestData);        
        if($PayPal->APICallSuccessful($PayPalResult['ACK']))
        {            
            echo json_encode(array('Ack'=>'Success','RedirectURL'=>$PayPalResult['REDIRECTURL']));
        }
        else
        {
            echo json_encode(array('Ack'=>'Failure','ErrorCode'=>$PayPalResult['L_ERRORCODE0'],'ErrorShort'=>$PayPalResult['L_SHORTMESSAGE0'],'ErrorLong'=>$PayPalResult['L_LONGMESSAGE0']));            
        }
        exit;
    }    
    
    public static function ifthengive_my_transactions(){
        $table = new AngellEYE_IfThenGive_My_Transactions_Table();
        $my_transactions_data = $table->get_transactions();        
        $recordsTotal = $table->record_count();
        if(!empty($my_transactions_data))
            echo json_encode(array('recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsTotal,'data'=>$my_transactions_data));
        else {
            echo json_encode(array('recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsTotal,'data'=>''));
        }
        exit;
    }
    
    public static function ifthengive_my_goals(){
        $table = new AngellEYE_IfThenGive_My_Goals_Table();
        $my_goals_data = $table->get_goals();        
        $recordsTotal = $table->record_count();
        if(!empty($my_goals_data))
            echo json_encode(array('recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsTotal,'data'=>$my_goals_data));
        else {
            echo json_encode(array('recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsTotal,'data'=>''));
        }
        exit;
    }
    
    public static function cancel_my_account_ba(){        
        $user_id = $_POST['userid'];
        $billing_agreement_id = get_user_meta( $user_id, 'itg_gec_billing_agreement_id', true );
        $PayPal_config = new AngellEYE_IfThenGive_PayPal_Helper();        
        $PayPal_config->set_api_cedentials();        
        $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
        /*
         *   By default Angell EYE PayPal PHP Library has ButtonSource is "AngellEYE_PHPClass".
         *   We are overwirting that variable with "AngellEYE_IfThenGive" value.
         *   It also reflactes in NVPCredentials string so we are also replcing it.
         */
        $PayPal->APIButtonSource = ITG_BUTTON_SOURCE;
        $PayPal->NVPCredentials = str_replace('AngellEYE_PHPClass',ITG_BUTTON_SOURCE,$PayPal->NVPCredentials);        

        $BAUpdateFields = array(
            'REFERENCEID' => $billing_agreement_id,           
            'BILLINGAGREEMENTSTATUS' => 'Canceled',
            'BILLINGAGREEMENTDESCRIPTION' => 'Cancel Billing Agreement'
        );        
        $PayPalRequestData = array('BAUFields' => $BAUpdateFields);        
        $PayPalResult = $PayPal->BillAgreementUpdate($PayPalRequestData);  
        if($PayPalResult['RAWRESPONSE'] == false){
            echo __("PayPal Timeout Error.",ITG_TEXT_DOMAIN);
        }
        elseif (!empty ($PayPalResult['ERRORS'])){
            echo $PayPal->DisplayErrors($PayPalResult['ERRORS']);            
        }
        elseif($PayPal->APICallSuccessful($PayPalResult['ACK'])){
            $goals = AngellEYE_IfThenGive_My_Goals_Table::get_all_goal_ids($user_id);
            foreach ($goals as $goal){                        
                update_user_meta( $user_id, 'itg_giver_'.$goal['goal_id'].'_status', 'suspended' );
            }
            update_user_meta( $user_id, 'itg_gec_billing_agreement_id','');
            update_user_meta( $user_id, 'itg_signedup_goals','');
            echo __("Successfully Cancelled",ITG_TEXT_DOMAIN);
        }
        else{
            echo __("Something went wrong",ITG_TEXT_DOMAIN);            
        } 
        exit;
    }
    
    public static function itg_adjust_amount(){
        if(isset($_POST['changed_amount'])){
            $changed_amount = $_POST['changed_amount'];
            $postid = $_POST['postid'];
            update_post_meta( $postid,'itg_signup_amount',$changed_amount);
        }        
        exit;
    }
    
    public static function change_giver_status(){       
        if(isset($_POST['userId'])){
            $user_id = $_POST['userId'];
            $data = get_user_meta($user_id,'itg_giver_'.$_POST['goalId'].'_status',true);
            if(empty($data)){
               update_user_meta( $user_id , 'itg_giver_'.$_POST['goalId'].'_status', 'suspended' );
            }
            elseif($data == 'suspended'){
                update_user_meta( $user_id , 'itg_giver_'.$_POST['goalId'].'_status', 'active' );
            }
            else{
                update_user_meta( $user_id , 'itg_giver_'.$_POST['goalId'].'_status', 'suspended' );
            }
        }
        exit;
    }
    
}

AngellEYE_IfThenGive_Public_Display::init();