<?php
class AngellEYE_Give_When_PayPal_Permission_Callback {
    
    public static function init() {
        
        if(!empty($_GET['request_token']) && !empty($_GET['verification_code'])){
            include 'Angelleye_PayPal.php';
            include 'Adaptive.php';
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
            
            $PayPal = new Adaptive($PayPalConfig);

            // Prepare request arrays
            $GetAccessTokenFields = array(
                'Token' => $_REQUEST['request_token'], 	
                'Verifier' => $_REQUEST['verification_code']
                );

            $PayPalRequestData = array('GetAccessTokenFields' => $GetAccessTokenFields);

            // Pass data into class for processing with PayPal and load the response array into $PayPalResult
            $PayPalResult = $PayPal->GetAccessToken($PayPalRequestData);
            echo "<pre>";
            var_dump($PayPalResult);   
        }
    }
}
AngellEYE_Give_When_PayPal_Permission_Callback::init();