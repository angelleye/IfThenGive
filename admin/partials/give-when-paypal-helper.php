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
                $this->set_api_cedentials();
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
                $this->set_api_cedentials();
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
        
        public function set_api_subject($API_SUBJECT){
            $this->api_subject = $API_SUBJECT;
            return true;
        }
        
        public function set_api_cedentials(){           
            $sanbox_enable = get_option('sandbox_enable_give_when', TRUE);            
            if($sanbox_enable === 'yes'){
                $this->sandbox=TRUE;
                $this->api_username='tejasm-merchant_api2.itpathsolutions.co.in';
                $this->api_password='GJA2TBCF3U9H4VK9';
                $this->api_signature='AFcWxV21C7fd0v3bYYYRCpSSRl31A47TBRQKcZyw6Bx9aDcmqr9ipPmt';
                $this->application_id='APP-80W284485P519543T';
            }            
            else{
                $this->sandbox='';
                $this->api_username='';
                $this->api_password='';
                $this->api_signature='';
                $this->application_id='';
            }   
        }
        
}
