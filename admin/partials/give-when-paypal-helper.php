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

    public static function get_configuration(){
            $sandbox=TRUE;
            $developer_account_email = '';
            $application_id = 'APP-80W284485P519543T';
            $device_id = '';
            $api_username =  'tejasm-merchant_api2.itpathsolutions.co.in';
            $api_password =  'GJA2TBCF3U9H4VK9';
            $api_signature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31A47TBRQKcZyw6Bx9aDcmqr9ipPmt';
            $api_subject = '';
            $print_headers = '';
            $log_results = '';
            $log_path = '';
            $PayPalConfig = array(
                'Sandbox' => $sandbox,
                'DeveloperAccountEmail' => $developer_account_email,
                'ApplicationID' => $application_id,
                'DeviceID' => $device_id,
                'IPAddress' => $_SERVER['REMOTE_ADDR'],
                'APIUsername' => $api_username,
                'APIPassword' => $api_password,
                'APISignature' => $api_signature,
                'APISubject' => $api_subject,
                'PrintHeaders' => $print_headers, 
                'LogResults' => $log_results, 
                'LogPath' => $log_path,
            );
            return $PayPalConfig;
    }
}