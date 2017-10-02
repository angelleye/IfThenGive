<?php

/**
 * This class defines all paypal custom functions
 * @class       Give_When_PayPal_Helper
 * @version	1.0.0
 * @package	GiveWhen/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class Give_When_PayPal_Helper {
       
        var $sandbox='';
        var $developer_account_email = '';
        var $application_id = '';
        var $device_id = '';
        var $api_username =  '';
        var $api_password =  '';
        var $api_signature = '';
        var $api_subject = '';
        var $print_headers = '';
        var $log_results = '';
        var $log_path = '';
        var $token='';
        var $token_secret='';                              
        
        public function get_configuration(){
            
                $PayPalConfig = array(
                    'Sandbox' => $this->sandbox,
                    'DeveloperAccountEmail' => $this->developer_account_email,
                    'ApplicationID' => $this->application_id,
                    'DeviceID' => $this->device_id,
                    'IPAddress' => $_SERVER['REMOTE_ADDR'],
                    'APIUsername' => $this->api_username,
                    'APIPassword' => $this->api_password,
                    'APISignature' => $this->api_signature,
                    'APISubject' => $this->api_subject,
                    'PrintHeaders' => $this->print_headers, 
                    'LogResults' => $this->log_results, 
                    'LogPath' => $this->log_path,
                );
                return $PayPalConfig;
        }
    
        public function get_third_party_configuration(){
                $PayPalConfig = array(
                    'Sandbox' => $this->sandbox,
                    'DeveloperAccountEmail' => $this->developer_account_email,
                    'ApplicationID' => $this->application_id,
                    'DeviceID' => $this->device_id,
                    'IPAddress' => $_SERVER['REMOTE_ADDR'],
                    'APIUsername' => $this->api_username,
                    'APIPassword' => $this->api_password,
                    'APISignature' => $this->api_signature,
                    'APISubject' => $this->api_subject,
                    'PrintHeaders' => $this->print_headers, 
                    'LogResults' => $this->log_results, 
                    'LogPath' => $this->log_path,
                    'ThirdPartyPermission' => TRUE,
                    'Token' => $this->token,
                    'TokenSecret' => $this->token_secret
                );
                return $PayPalConfig;
        }
    
        public function set_tokens($token,$token_secret){
            $this->token = $token;
            $this->token_secret = $token_secret;
            return true;
        }
        
        public function set_api_subject($goal_id){
            $this->api_subject = apply_filters('givewhen_api_subject_parameter','',$goal_id);
            return true;
        }
        
        public function set_api_cedentials(){
            $sanbox_enable = get_option('sandbox_enable_give_when');            
            if($sanbox_enable === 'yes'){
                 $this->sandbox=TRUE;                 
                 $this->api_username=get_option('give_when_sandbox_api_credentials_api_user_name');
                 $this->api_password=get_option('give_when_sandbox_api_credentials_api_password');
                 $this->api_signature=get_option('give_when_sandbox_api_credentials_signature');
                 $this->application_id='APP-80W284485P519543T';
              }
              else{
                 $this->sandbox='';
                 $this->api_username=get_option('give_when_live_api_credentials_api_user_name');
                 $this->api_password=get_option('give_when_live_api_credentials_api_password');
                 $this->api_signature=get_option('give_when_live_api_credentials_signature');
                 $this->application_id='';
             }
        }
        
        public function get_currency_symbol($Currency){
            $CurrencyCodes = array(
                'AUD' => '&#36;',         // Austrailian Dollar
                'BRL' => '&#82;&#36;',    //Brazilian Real
                'CAD' => '&#36;',         //Canadian Dollar
                'CZK' => '&#75;&#269;',   //Czeck Koruna
                'DKK' => '&#107;&#114;',  //Danish Krone
                'EUR' => '&#8364;',       //Euro
                'HKD' => '&#36;',         //Hong Kong Dollar
                'HUF' => '&#70;&#116;',   //Hungarian Forint
                'ILS' => '&#8362;',       //Israeli New Sheqel
                'JPY' => '&#165;',        //Japanese Yen
                'MYR' => '&#82;&#77;',    //Malaysian Ringgit
                'MXN' => '&#36;',         //Mexican Peso
                'NOK' => '&#107;&#114;',  //Norwegian Krone
                'NZD' => '&#36;',         //New Zealand Dollar
                'PHP' => '&#8369;',       //Philippine Peso
                'PLN' => '&#122;&#322;',  //Polish Zloty
                'GBP' => '&#163;',        //Pound Sterling
                'SGD' => '&#36;',         //Singapore Dollar
                'SEK' => '&#107;&#114;',  //Swedish Krona
                'CHF' => '&#67;&#72;&#70;', //Swiss Franc
                'TWD' => '&#78;&#84;&#36;', //Taiwan New Dollar
                'THB' => '&#3647;',         //Thai Baht
                'USD' => '&#36;',          //U.S. Dollar
                );
            if (array_key_exists($Currency,$CurrencyCodes)){
                return $CurrencyCodes[$Currency];
            }
            else{
                return false;
            }
        }
}
