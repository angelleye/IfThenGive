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
        add_action('init', array(__CLASS__,'ifthengive_call_shortcode'));
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
        global $post,$wp_query;
        $is_endpoint = false;
        if(isset($wp_query->query_vars['itgmyaccount'])){
            $is_endpoint = true;
        }
        if($post){
            if(has_shortcode( $post->post_content, 'ifthengive_account') || $is_endpoint == true){
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
    public static function ifthengive_call_shortcode(){
        add_shortcode('ifthengive_goal', array(__CLASS__, 'ifthengive_create_shortcode'));
    }

    /**
     * ifthengive_create_shortcode function is for generate
     * @since 1.0.0
     * @access public
     */
    public static function ifthengive_create_shortcode($atts, $content = null) {        
        if(!is_admin()){
            ob_start();
            Ifthengive_Public::itg_get_template('display-goal',$atts);
            return ob_get_clean();
        }
    }
    
    public static function validate_fields($itguser){        
        /* validation starts */
        $ValidationErrors = array();
        $fname = sanitize_text_field( $itguser['ifthengive_firstname']);
        if (!preg_match("/^[a-zA-Z]+$/",$fname)) {
          $ValidationErrors['FirstName'] = __("Invalid Input : Only letters allowed in First Name",'ifthengive');
        }
        $lname = sanitize_text_field($itguser['ifthengive_lastname']);
        if (!preg_match("/^[a-zA-Z]+$/",$lname)) {
          $ValidationErrors['LastName'] = __("Invalid Input : Only letters allowed in Last Name",'ifthengive');
        }

        $email = sanitize_email($itguser['ifthengive_email']);
        if (!is_email($email)) {
            $ValidationErrors['Email'] = __("Invalid email format",'ifthengive');
        }
        if(isset($itguser['ifthengive_password'])){
            if ($itguser['ifthengive_password'] !== $itguser['ifthengive_retype_password']) {
                $ValidationErrors['Password'] = __("Mismatch Input : Password Fields are not matched",'ifthengive');
            }
        }
        if(isset($itguser['itg_signup_as_guest']) && $itguser['itg_signup_as_guest']=='on'){
            if (isset($itguser['ifthengive_password']) && empty($itguser['ifthengive_password'])) {
                $ValidationErrors['PasswordRequired'] = __("Mismatch Input : Password Field is required",'ifthengive');
            }
        }
        if(!empty($ValidationErrors)){
            echo json_encode(array('Ack'=>__('ValidationError','ifthengive'),'ErrorCode'=>__('Invalid Inputs','ifthengive'),'ErrorLong'=>__('Please find Following Error','ifthengive'),'Errors'=>$ValidationErrors));
            exit;
        }            
        /* validation End */
        return true;
    }
    
    
    public static function get_userdata_from_userid($user_id){
        $theUser = new WP_User($user_id);
        $userdata['ID'] = $user_id;
        $userdata['user_email'] = isset($theUser->data->user_email) ? $theUser->data->user_email : '' ;
        $userdata['user_nicename'] = isset($theUser->data->user_nicename) ? $theUser->data->user_nicename : '';
        $userdata['user_login'] = isset($theUser->data->user_login) ? $theUser->data->user_login : '';
        $userdata['display_name'] = isset($theUser->data->display_name) ? $theUser->data->display_name : '';
        $userdata['first_name'] = isset($theUser->data->first_name) ? $theUser->data->first_name : '';
        $userdata['last_name'] = isset($theUser->data->last_name) ? $theUser->data->last_name : '';
        /*if user is admin then no change in the role*/
        $is_admin = user_can($user_id, 'manage_options' );
        if($is_admin){
            /* Do nothing */
        }else{
            /* if user is not admin then add additional role to the current user */
            $theUser->add_role( 'giver' );
        }
        return $userdata;
    }
    
    public static function is_already_registerd($user_id,$goal_id){        
        /*Check if user is already signed up for this goal then get him back with info.*/
        $signnedup_goals = get_user_meta($user_id,'itg_signedup_goals');        
        $goalArray = explode('|', $signnedup_goals[0]);                
        if(!empty($goalArray)){
            if(in_array($goal_id, $goalArray)){
                return true;
            }
        }
        else{
            return false;
        }        
    }
    
    public static function have_billing_agreement($user_id){
        /*Check if user have already a Billing Agreement then add just signedup for that goal and get it back with info */
        $isAvailableBAID = get_user_meta($user_id,'itg_gec_billing_agreement_id',true);
        if(!empty($isAvailableBAID)){
            return true;
        }        
        return false;        
    }
    
    public static function add_goal_to_signup_list($user_id,$amount,$goal_id){
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        /* Create new post for signup post type and save goal_id,user_id,amount */
        $new_post_id = wp_insert_post( array(
            'post_author' => $user_id,
            'post_status' => 'publish',
            'post_type' => 'itg_sign_up',
            'post_title' => ('User ID : '.$user_id.'& Goal ID : '.$goal_id)
        ) );

        update_post_meta($new_post_id,'itg_signup_amount',  $amount);
        update_post_meta($new_post_id,'itg_signup_wp_user_id',$user_id);
        update_post_meta($new_post_id,'itg_signup_wp_goal_id',$goal_id);
        update_post_meta($new_post_id, 'itg_signup_in_sandbox', $sandbox);
        update_post_meta($new_post_id,'itg_transaction_status','0');

        $amount = base64_encode($amount);
        $post = get_post($goal_id); 
        $slug = $post->post_name;
        $urlusr = base64_encode($user_id);
        $REDIRECTURL = site_url('itg-thankyou?goal='.$slug.'&amt='.$amount.'&user='.$urlusr);
        /* Add post id in the user's signedup goals */
        $signedup_goals= get_user_meta($user_id,'itg_signedup_goals',true);
        if($signedup_goals !=''){
        $signedup_goals = $signedup_goals."|".$goal_id;
        }
        else{
            $signedup_goals = $goal_id;
        }        
        //wp_set_auth_cookie( $user_id, true ); disables this because user is login here.
        update_user_meta($user_id,'itg_signedup_goals',$signedup_goals);
        update_user_meta($user_id,'itg_giver_'.$goal_id.'_status','active');                                
        echo json_encode(array('Ack'=>'Success','RedirectURL'=>$REDIRECTURL));
        exit;
    }
    
    public static function set_express_checkout($goal_id,$amount,$cancel_page){
        
        $brandname = get_option('itg_brandname');
        $logoimg = get_option('itg_brandlogo');
        $hdlogoimg = get_option('itg_hd_brandlogo');
        $customerservicenumber = get_option('itg_cs_number');
        
        /*Get trigger_name of Post */        
        $trigger_name = get_post_meta( $goal_id, 'trigger_name', true );
        
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
                'l_billingagreementcustom' => 'ifthengive_'.$goal_id
        );
        array_push($BillingAgreements, $Item);

        $PayPalRequestData = array(
            'SECFields' => $SECFields, 
            'Payments' => $Payments,
            'BillingAgreements' => $BillingAgreements,
        );
        $PayPalResult = $PayPal->SetExpressCheckout($PayPalRequestData);        
        if($PayPalResult['RAWRESPONSE'] != false){
            if(isset($PayPalResult['ACK'])){
                if($PayPal->APICallSuccessful($PayPalResult['ACK']))
                {            
                    echo json_encode(array('Ack'=>'Success','RedirectURL'=>$PayPalResult['REDIRECTURL']));
                }
                else
                {
                    echo json_encode(array('Ack'=>'Failure','ErrorCode'=>$PayPalResult['L_ERRORCODE0'],'ErrorShort'=>$PayPalResult['L_SHORTMESSAGE0'],'ErrorLong'=>$PayPalResult['L_LONGMESSAGE0']));            
                }
            }
            else{
                echo json_encode(array('Ack'=>'Failure','ErrorShort'=>__('No PayPal Acknowledgement','ifthengive'),'ErrorLong'=>__('No PayPal Acknowledgement','ifthengive')));
            }
        }
        else{
            echo json_encode(array('Ack'=>'Failure','ErrorShort'=>__('Something went wrong.','ifthengive'),'ErrorLong'=>__('PayPal Timeout issue or SSL issue.','ifthengive')));
        }
    }
    
    public static function process_before_sec($user_id,$goal_id,$amount,$cancel_page,$itg_guest_user){
        // check if user have billing agreement
            if (self::have_billing_agreement($user_id)){
                // check if user is already register for the goal
                if(self::is_already_registerd($user_id, $goal_id)){                    
                    echo json_encode(array('Ack'=>__('Information','ifthengive'),'ErrorShort'=>__('You are already signed up for this goal.','ifthengive'),'ErrorLong'=>__('We already have a record of this email address signed up for this goal.','ifthengive')));
                    exit;
                }
                else{
                   /* User have Billing Agreement but not signedup for the goal                     
                    * that means adding goals to just in itg_signedup_goals user meta                     
                    */
                    self::add_goal_to_signup_list($user_id,$amount,$goal_id);
                }
            }
            else{               
                /* user is login but signinup for the first time.
                 * User doesn't have billing agreement so
                 * Process user for BA in PayPal.
                 */
                /* get user data and set them for session */
                $userdata = self::get_userdata_from_userid($user_id);
                
                $_SESSION['itg_user_data'] = $userdata;          
                $_SESSION['itg_signup_amount'] = $amount;
                $_SESSION['itg_signup_wp_user_id'] = $user_id;
                $_SESSION['itg_signup_wp_goal_id'] = $goal_id;
                $_SESSION['itg_guest_user'] = $itg_guest_user;
                self::set_express_checkout($goal_id, $amount, $cancel_page);
                exit;
            }
    }

    public static function verify_submitted_nonce($nonce_value,$nonce_key){        
        if (!wp_verify_nonce(  $nonce_value ,  $nonce_key  )  ) {
            return false;
        }
        return true;
    }

    public static function start_express_checkout(){
        //global $wpdb;
        /*Getting data from ajax */        
        $post_id = sanitize_key($_POST['post_id']);
        $amount = filter_var(number_format($_POST['amount'],2,'.', ''), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        /* Get user information  from Form Data. */
        $itguser = array();
        parse_str($_POST['formData'], $itguser);       
        
        if(self::verify_submitted_nonce($itguser['_itg_goal_form_nonce'],'itg_goal_form') == false){            
            echo json_encode(array(
                'Ack'=>'Failure',                
                'ErrorShort'=>__('Invalid nonce','ifthengive'),
                'ErrorLong'=>__('WordPress nonce verificaion failed.','ifthengive')
                ));
            exit;
        }
                    
        self::validate_fields($itguser);
        
         /*if no role defined in the code then it adds new role as giver */
        $role = get_role( 'giver' );
        if($role==NULL){
            add_role('giver','Giver');
        }
        
        if(!session_id()) {
            session_start();
        }
        /*Create cancel page url like return to the cancel page from where it goes.*/        
        $cancel_page = $itguser['ifthengive_page_id'];
        
        if(isset($itguser['itg_signup_as_guest']) && $itguser['itg_signup_as_guest']=='on' ){
            $itg_guest_user = 'no';
        }
        else{
            $itg_guest_user = 'yes';
        }
        
        if(is_user_logged_in()){
            // user is login            
            $current_user    = wp_get_current_user();
            $user_id = $current_user->ID;
            $itg_guest_user = 'no';
            self::process_before_sec($user_id, $post_id, $amount, $cancel_page,$itg_guest_user);
        }
        else{
            /* User is not login */
            /* Check if the entered email is already in wordpress */
            $user_email = email_exists(sanitize_email($itguser['ifthengive_email']));
            if($user_email){
                $user_id =$user_email;
                $itg_guest_user = 'no';
                self::process_before_sec($user_id, $post_id, $amount, $cancel_page,$itg_guest_user);
            }
            else{
                /*
                 *   User is sigining up for the first time
                 *   that means always new user.               
                 */
                /*Create array of user data */
                $userdata=array(
                    'user_pass' => isset($itguser['ifthengive_password']) ? sanitize_text_field($itguser['ifthengive_password']) : '',
                    'user_login' => isset($itguser['ifthengive_email']) ? sanitize_email($itguser['ifthengive_email']) : '',
                    'user_email' => isset($itguser['ifthengive_email']) ? sanitize_email($itguser['ifthengive_email']) : '',
                    'display_name' => sanitize_text_field($itguser['ifthengive_firstname']).' '.sanitize_text_field($itguser['ifthengive_lastname']),
                    'first_name' => isset($itguser['ifthengive_firstname']) ? sanitize_text_field($itguser['ifthengive_firstname']) : '',
                    'last_name' => isset($itguser['ifthengive_lastname']) ? sanitize_text_field($itguser['ifthengive_lastname']) : '',
                    'role' => 'giver'
                );
                
                $_SESSION['itg_user_data'] = $userdata;          
                $_SESSION['itg_signup_amount'] = $amount;
                $_SESSION['itg_signup_wp_user_id'] = $user_id;
                $_SESSION['itg_signup_wp_goal_id'] = $post_id;
                $_SESSION['itg_guest_user'] = $itg_guest_user;
                /*Create cancel page url like return to the cancel page from where it goes.*/        
                $cancel_page = $itguser['ifthengive_page_id'];
                self::set_express_checkout($post_id, $amount, $cancel_page);
                exit;
            }
        }
               
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
        $user_id = sanitize_text_field($_POST['userid']);
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
            echo __("PayPal Timeout Error.",'ifthengive');
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
            echo __("Successfully Cancelled",'ifthengive');
        }
        else{
            echo __("Something went wrong",'ifthengive');            
        } 
        exit;
    }
    
    public static function itg_adjust_amount(){
        if(isset($_POST['changed_amount'])){
            $changed_amount = sanitize_text_field($_POST['changed_amount']);
            $postid = sanitize_key($_POST['postid']);
            update_post_meta( $postid,'itg_signup_amount',$changed_amount);
        }        
        exit;
    }
    
    public static function change_giver_status(){       
        if(isset($_POST['userId'])){
            $user_id = sanitize_key($_POST['userId']);
            $data = get_user_meta($user_id,'itg_giver_'.sanitize_key($_POST['goalId']).'_status',true);
            if(empty($data)){
               update_user_meta( $user_id , 'itg_giver_'.sanitize_key($_POST['goalId']).'_status', 'suspended' );
            }
            elseif($data == 'suspended'){
                update_user_meta( $user_id , 'itg_giver_'.sanitize_key($_POST['goalId']).'_status', 'active' );
            }
            else{
                update_user_meta( $user_id , 'itg_giver_'.sanitize_key($_POST['goalId']).'_status', 'suspended' );
            }
        }
        exit;
    }
    
}

AngellEYE_IfThenGive_Public_Display::init();