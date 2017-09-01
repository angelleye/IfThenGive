<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 *
 * @package    Givewhen
 * @subpackage Givewhen/public/partials
 */
class AngellEYE_Give_When_Public_Display {

    public static function init() {
        add_shortcode('give_when_goal', array(__CLASS__, 'give_when_create_shortcode'));
        add_action( 'wp_enqueue_scripts', array(__CLASS__,'give_when_detect_shortcode'));
        add_action( 'wp_ajax_start_express_checkout', array(__CLASS__,'start_express_checkout'));
        add_action("wp_ajax_nopriv_start_express_checkout",  array(__CLASS__,'start_express_checkout'));
        add_action( 'wp_ajax_givewhen_my_transactions', array(__CLASS__,'givewhen_my_transactions'));
        add_action("wp_ajax_nopriv_givewhen_my_transactions",  array(__CLASS__,'givewhen_my_transactions'));
    }
   
    public static function give_when_detect_shortcode()
    {
        global $post;
        $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches )
            && in_array( 'give_when_goal', $matches[2] ) )
        {            
            //wp_enqueue_style( 'givewhen-one', GW_PLUGIN_URL . 'includes/css/bootstrap/css/bootstrap.css', array(), '1.0.0','all' );
        }
    }
          
    /**
     * give_when_create_shortcode function is for generate
     * @since 1.0.0
     * @access public
     */
    public static function give_when_create_shortcode($atts, $content = null) {
        global $post, $post_ID; 
        $give_when_page_id = $post->ID;
        extract(shortcode_atts(array(
                    'id' => ''), $atts));
        $html = '';
        $ccode = get_option('gw_currency_code');
        $paypal = new Give_When_PayPal_Helper();
        $symbol = $paypal->get_currency_symbol($ccode);
        if( !empty($id) ) {
            $post = get_post($id);
            if(!empty($post->post_type) && $post->post_type == 'give_when_goals' && $post->post_status == 'publish') {
        
                $html .= '<div id="overlay" style=" background: #d9d9da;opacity: 0.9;width: 100%;float: left;height: 100%;position: fixed;top: 0;left:0;right:0;z-index: 1031;text-align: center; display: none;">';
                $html .=  '<div class="gw_loader"></div>
                           <h1 style="font-weight: 600;">'.esc_html('Processing...','givewhen').'</h1></div>';
                $html .= '</div>';
                $html .= '<div class="gw_container">';
                    $html .= '<div class="gw_post-item">';                           
                            
                            $html .= '<div class="gw_post-title">
                                        <h3>'.get_post_meta( $post->ID, 'trigger_name', true ).'</h3>
                                      </div>';
                            
                            $html .= '<div class="gw_post-image">';
                            $html .= '<img src="'.get_post_meta( $post->ID, 'image_url', true ).'">';
                            $html .= '</div>';

                            $html .= '<div class="gw_post-content-details">'; 
                                $html .= '<div class="gw_post-description" id="scrolltopid">
                                            <p>'.get_post_meta( $post->ID, 'trigger_desc', true ).'</p>
                                          </div>';
                                $html .= $post->post_content;                                                                                
                                $amount = get_post_meta($post->ID,'amount',true);

                                if($amount == 'fixed'){
                                    $html .= '<div class="gw_post-title">';
                                    $fixed_amount = get_post_meta($post->ID,'fixed_amount_input',true);                                
                                    $html .= '<h4>'. esc_html('I will Give ','givewhen').$symbol.'<span id="give_when_fixed_price_span">'.number_format($fixed_amount,2).'</span> '. esc_html('When','').'&nbsp;'.get_post_meta( $post->ID, 'trigger_thing', true ).'</h4>';
                                    $html .= '</div>';                                    
                                }                                
                                elseif($amount == 'manual'){
                                    $manual_amount_input_value = get_post_meta($post->ID, 'manual_amount_input', true);
                                    $html .= '<div class="gw_post-title">';
                                        $html .= '<h4>'.esc_html('I will Give ','givewhen').$symbol.'<span id="give_when_manual_price_span">'.$manual_amount_input_value.'</span> '.esc_html('When','').'&nbsp;'.get_post_meta( $post->ID, 'trigger_thing', true ).'</h4>';
                                    $html .= '</div>';
                                    $html .= '<div class="gw_form-group">';
                                        $html .= '<label for="manualamout" class="gw_upper">'. esc_html('Enter Amount','').'</label>';
                                        $html .= '<input type="text" name="gw_manual_amount_input" value="'.$manual_amount_input_value.'" class="gw_form-control" autocomplete="off" id="gw_manual_amount_input" placeholder="Enter Amount"/>';
                                    $html .= '</div>';
                                }
                                else{
                                    $option_name = get_post_meta($post->ID,'option_name',true);
                                    $option_amount = get_post_meta($post->ID,'option_amount',true);
                                    $i=0;
                                    $html .= '<div class="gw_post-title">';
                                        $html .= '<h4>'.esc_html('I will Give ','givewhen').$symbol.'<span id="give_when_fixed_price_span_select">'.number_format($option_amount[0],2).'</span> '. esc_html('When','').'&nbsp;'.get_post_meta( $post->ID, 'trigger_name', true ).'</h4>';
                                    $html .= '</div>';
                                    $html .= '<div class="gw_form-group">';
                                        $html .= '<select class="gw_form-control" name="give_when_option_amount" id="give_when_option_amount">';
                                
                                        foreach ($option_name as $name) {
                                             $html .=  '<option value="'.number_format($option_amount[$i],2).'">'.$name." ".number_format($option_amount[$i],2).'</option>';                                        
                                        $i++;
                                        }
                                        $html .= '</select>';
                                    $html .= '</div>';
                                }
                        $html .= '</div>'; // gw_post-content-details
                    $html .= '</div>'; // gw_post-item 
                                       
                    $html .= '<div class="gwcontainer" id="give_when_signup_form">';                 
                        $html .= '<div class="gw_hr-title gw_center">';
                        $html .= '<abbr>'.esc_html('Sign up for ',''). get_post_meta( $post->ID, 'trigger_name', true ).'</abbr>';
                        $html .= '</div>';
                                                             
                        $html .= '<div class="gw_alert gw_alert-warning" id="connect_paypal_error_public" style="display: none">';
                        $html .= '<span id="connect_paypal_error_p"></span>';
                        $html .= '</div>';
                        
                        $html .= '<p class="text-info">'.__('Instructions','givewhen').'</p>';
                        $html .='<ol>
                                    <li>'.__('Lorem ipsum dolor sit amet.','givewhen').'</li>
                                    <li>'.__('Consectetur adipiscing elit.','givewhen').'</li>
                                    <li>'.__('Integer molestie lorem at massa.','givewhen').'</li>
                                </ol>';
                                    
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
                                    
                                    $html .= '<form method="post" name="signup" id="give_when_signup">';
                                        $html .= '<div class="gw_form-group">';                                        
                                          $html .= '<label class="gw_upper" for="name">'.esc_html('First Name','givewhen').'</label>';
                                          $html .= '<input type="text" class="gw_form-control" name="give_when_firstname" id="give_when_firstname" autocomplete="off" required="required" value="'.$User_first_name.'">';
                                        $html .= '</div>';
                                        $html .= '<div class="gw_form-group">';
                                          $html .= '<label class="gw_upper" for="name">'.esc_html('Last Name','givewhen').'</label>';
                                          $html .= '<input type="text" class="gw_form-control" name="give_when_lastname" id="give_when_lastname" autocomplete="off" required="required" value="'. $User_last_name.'">';
                                        $html .= '</div>';
                                        $html .= '<div class="gw_form-group">';
                                          $html .= '<label class="gw_upper" for="email">'. esc_html('Email address','givewhen').'</label>';
                                          $html .= '<input type="email" class="gw_form-control" name="give_when_email" id="give_when_email" autocomplete="off" required="required" value="'.$User_email.'">';
                                        $html .= '</div>';                                                                            
                                        $html .=  '<div class="checkbox">';
                                        $html .=    '<label class="gw_upper">';
                                        $html .=      '<input type="checkbox" name="gw_signup_as_guest" id="gw_signup_as_guest" checked>'.esc_html('Create a account','givewhen');
                                        $html .=    '</label>';
                                        $html .=  '</div><br>';
                                         if ( ! is_user_logged_in() ) {                                    
                                        $html .= '<div class="gw_form-group gw-password">';
                                          $html .= '<label class="gw_upper" for="password">'.esc_html('Password','givewhen').'</label>';
                                          $html .= '<input type="password" class="gw_form-control" name="give_when_password" id="give_when_password" required="required">';
                                        $html .= '</div>';
                                        $html .= '<div class="gw_form-group gw-password">';
                                          $html .= '<label class="gw_upper" for="password">'.esc_html('Re-type Password','givewhen').'</label>';
                                          $html .= '<input type="password" class="gw_form-control" name="give_when_retype_password" id="give_when_retype_password" required="required">';
                                        $html .= '</div>';
                                         }                                        
                                        $html .= '<input type="hidden" name="give_when_page_id" id="give_when_page_id" value="'.$give_when_page_id.'">';
                                        $html .= '<button type="button" class="gw_btn gw_btn-primary" id="give_when_angelleye_checkout" data-postid="'.$post->ID.'" data-userid="'.$user_id.'">'.esc_html('Sign Up For ','givewhen') . get_post_meta( $post->ID, 'trigger_name', true ).'</button>';
                                    $html .= '</form>';
                                $html .= '</div>'; // gwcontainer
                            $html .= '</div>'; // gw_container                        
            }
        }
        return $html;        
    }
         
    public function start_express_checkout(){        
        /*Getting data from ajax */        
        $post_id = $_POST['post_id'];
        $amount = number_format($_POST['amount'],2);        
        
        /* Get user information  from Form Data. */
        $gwuser = array();
        parse_str($_POST['formData'], $gwuser);
        
        /*valodation starts */
        $ValidationErrors = array();
        $fname = sanitize_text_field( $gwuser['give_when_firstname']);
        if (!preg_match("/^[a-zA-Z]+$/",$fname)) {
          $ValidationErrors['FirstName'] = __("Invalid Input : Only letters allowed in First Name",'givewhen');
        }
        $lname = sanitize_text_field($gwuser['give_when_lastname']);
        if (!preg_match("/^[a-zA-Z]+$/",$lname)) {
          $ValidationErrors['LastName'] = __("Invalid Input : Only letters allowed in Last Name",'givewhen');
        }

        $email = $gwuser['give_when_email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $ValidationErrors['Email'] = __("Invalid email format",'givewhen');
        }
        if(isset($gwuser['give_when_password'])){
            if ($gwuser['give_when_password'] !== $gwuser['give_when_retype_password']) {
                $ValidationErrors['Password'] = __("Mismatch Input : Password Fields are not matched",'givewhen');
            }
        }                                
        if(!empty($ValidationErrors)){
            echo json_encode(array('Ack'=>__('ValidationError','givewhen'),'ErrorCode'=>__('Invalid Inputs','givewhen'),'ErrorLong'=>__('Please find Following Error','givewhen'),'Errors'=>$ValidationErrors));
            exit;
        }            
        /*valodation End */            
            
        /*Get trigger_name of Post */        
        $trigger_name = get_post_meta( $post_id, 'trigger_name', true );       
        
        /*Create cancel page url like return to the cancel page from where it goes.*/
        $page_id = $gwuser['give_when_page_id'];
        $cancel_page =  get_permalink( $page_id );        
            
        /*if no role defined in the code then it adds new role as giver */
        $role = get_role( 'giver' );
        if($role==NULL){
            add_role('giver','Giver');
        }
        /*Create array of user data */
        $userdata=array(
                'user_pass' => md5($gwuser['give_when_password']),
                'user_login' => $gwuser['give_when_email'],
                'user_email' => $gwuser['give_when_email'],
                'display_name' => $gwuser['give_when_firstname'].' '.$gwuser['give_when_lastname'],
                'first_name' => $gwuser['give_when_firstname'],
                'last_name' => $gwuser['give_when_lastname'],
                'role' => 'giver'
        );                
        $user_exist = email_exists($gwuser['give_when_email']);
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
            // user not exist. i.e. Always new user
            $user_id = $_POST['login_user_id'];
        }        
        
        if(!empty($user_id)){
            // User login
            
            /*Check if user have already a Billing Agreement then add just signedup for that goal and get it back with info */
            $isAvailableBAID = get_user_meta($user_id,'give_when_gec_billing_agreement_id',true);        
            if(!empty($isAvailableBAID)){
                /*Check if user is already signed up for this goal then get him back with info.*/
                $signnedup_goals = get_user_meta($user_exist,'give_when_signedup_goals');        
                $goalArray = explode('|', $signnedup_goals[0]);                
                if(!empty($goalArray)){
                    if(in_array($post_id, $goalArray)){
                        echo json_encode(array('Ack'=>__('Information','givewhen'),'ErrorCode'=>__('GiveWhenInfo','givewhen'),'ErrorShort'=>__('You are already signed up for this goal.','givewhen'),'ErrorLong'=>__('You are already signed up for this goal.','givewhen')));
                        exit;
                    }
                }
                /* Create new post for signup post type and save goal_id,user_id,amount */
                $new_post_id = wp_insert_post( array(
                    'post_author' => $user_id,
                    'post_status' => 'publish',
                    'post_type' => 'give_when_sign_up',
                    'post_title' => ('User ID : '.$user_id.'& Goal ID : '.$post_id)
                ) );

                update_post_meta($new_post_id,'give_when_signup_amount',$amount);                    
                update_post_meta($new_post_id,'give_when_signup_wp_user_id',$user_id);
                update_post_meta($new_post_id,'give_when_signup_wp_goal_id',$post_id);
                
                $amount = base64_encode($amount);
                $post = get_post($post_id); 
                $slug = $post->post_name;
                $urlusr = base64_encode($user_id);
                $REDIRECTURL = site_url('give-when-thankyou?goal='.$slug.'&amt='.$amount.'&user='.$urlusr);                
                /* Add post id in the user's signedup goals */
                $signedup_goals= get_user_meta($user_id,'give_when_signedup_goals',true);
                if($signedup_goals !=''){
                $signedup_goals = $signedup_goals."|".$post_id;
                }
                else{
                    $signedup_goals = $post_id;
                }        
                wp_set_auth_cookie( $user_id, true );
                update_user_meta($user_id,'give_when_signedup_goals',$signedup_goals);
                echo json_encode(array('Ack'=>'Success','RedirectURL'=>$REDIRECTURL));
                exit;
            }
        }
        else{            
            // User not login I.e. Always new user            
        }

        /*Save user data in Session. */
        if(!session_id()) {
                session_start();
            }            
        
        if(isset($gwuser['gw_signup_as_guest']) && $gwuser['gw_signup_as_guest']=='on' ){
            $_SESSION['gw_guest_user'] = 'no';            
        }
        else{
            $_SESSION['gw_guest_user'] = 'yes';
            $userdata['user_pass'] = md5('GWPassword');
        }
        $_SESSION['gw_user_data'] = $userdata;
        
        /*PayPal setup */                
        $PayPal_config = new Give_When_PayPal_Helper();
        $PayPal_config->set_api_cedentials();
        $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
        /*
         *   By default Angell EYE PayPal PHP Library has ButtonSource is "AngellEYE_PHPClass".
         *   We are overwirting that variable with "AngellEYE_GiveWhen" value.
         *   It also reflactes in NVPCredentials string so we are also replcing it.
         */
        $PayPal->APIButtonSource = 'AngellEYE_GiveWhen';
        $PayPal->NVPCredentials = str_replace('AngellEYE_PHPClass','AngellEYE_GiveWhen',$PayPal->NVPCredentials);        
        $SECFields = array(
                'maxamt' => round($amount * 2,2),
                'returnurl' => site_url('?action=ec_return'),
                'cancelurl' => $cancel_page,
                'hdrimg' => 'https://www.angelleye.com/images/angelleye-paypal-header-750x90.jpg',
                'logoimg' => 'https://www.angelleye.com/images/angelleye-logo-190x60.jpg',
                'brandname' => 'Angell EYE',
                'customerservicenumber' => '816-555-5555',
        );
        $Payments = array();
        $Payment = array(
            'amt' => 0,            
            'custom' => 'amount_'.$amount.'|post_id_'.$post_id.'|user_id_'.$user_id
        );
        array_push($Payments, $Payment);
        
        $BillingAgreements = array();
        $Item = array(
                'l_billingtype' => 'MerchantInitiatedBilling',
                'l_billingagreementdescription' => $trigger_name,
                'l_paymenttype' => '',
                'l_billingagreementcustom' => 'give_when_'.$post_id
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
    
    public static function givewhen_my_transactions(){
        $table = new AngellEYE_Give_When_My_Transactions_Table();
        $my_transactions_data = $table->get_transactions();        
        $recordsTotal = $table->record_count();
        if(!empty($my_transactions_data))
            echo json_encode(array('recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsTotal,'data'=>$my_transactions_data));
        else {
            echo json_encode(array('recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsTotal,'data'=>''));
        }
        exit;
    }
}

AngellEYE_Give_When_Public_Display::init();