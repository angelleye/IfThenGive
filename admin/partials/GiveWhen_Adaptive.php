<?php

/**
 * PayPal Adaptive Payments Class
 *
 * This class houses all of the Adaptive Payments specific API's.  
 * @author    Andrew Angell <service@angelleye.com>
 */
require_once 'PPAuth.php';

class GiveWhen_Adaptive extends GiveWhen_Angelleye_PayPal {

    var $DeveloperAccountEmail = '';
    var $XMLNamespace = '';
    var $ApplicationID = '';
    var $DeviceID = '';
    var $IPAddress = '';
    var $DetailLevel = '';
    var $ErrorLanguage = '';
    var $ThirdPartyPermission = '';  // This is flag that indicate true if you are using third party permission.
    var $Token = '';                 // Token that you received after granted permission from third party
    var $TokenSecret = '';           // Token secret you received with the token.   

    /**
     * Constructor
     *
     * @access	public
     * @param	mixed[]	$DataArray	Array structure providing config data
     * @return	void
     */

    function __construct($DataArray) {
        parent::__construct($DataArray);

        $this->XMLNamespace = 'http://svcs.paypal.com/types/ap';
        $this->DeviceID = isset($DataArray['DeviceID']) ? $DataArray['DeviceID'] : '';
        $this->IPAddress = isset($DataArray['IPAddress']) ? $DataArray['IPAddress'] : $_SERVER['REMOTE_ADDR'];
        $this->DetailLevel = isset($DataArray['DetailLevel']) ? $DataArray['DetailLevel'] : 'ReturnAll';
        $this->ErrorLanguage = isset($DataArray['ErrorLanguage']) ? $DataArray['ErrorLanguage'] : 'en_US';
        $this->APISubject = isset($DataArray['APISubject']) ? $DataArray['APISubject'] : '';
        $this->DeveloperAccountEmail = isset($DataArray['DeveloperAccountEmail']) ? $DataArray['DeveloperAccountEmail'] : '';
        $this->ThirdPartyPermission = isset($DataArray['ThirdPartyPermission']) ? $DataArray['ThirdPartyPermission'] : '';
        $this->Token = isset($DataArray['Token']) ? $DataArray['Token'] : '';
        $this->TokenSecret = isset($DataArray['TokenSecret']) ? $DataArray['TokenSecret'] : '';

        if ($this->Sandbox) {
            // Sandbox Credentials
            $this->ApplicationID = isset($DataArray['ApplicationID']) ? $DataArray['ApplicationID'] : '';
            $this->APIUsername = isset($DataArray['APIUsername']) && $DataArray['APIUsername'] != '' ? $DataArray['APIUsername'] : '';
            $this->APIPassword = isset($DataArray['APIPassword']) && $DataArray['APIPassword'] != '' ? $DataArray['APIPassword'] : '';
            $this->APISignature = isset($DataArray['APISignature']) && $DataArray['APISignature'] != '' ? $DataArray['APISignature'] : '';
            $this->EndPointURL = isset($DataArray['EndPointURL']) && $DataArray['EndPointURL'] != '' ? $DataArray['EndPointURL'] : 'https://svcs.sandbox.paypal.com/';
        } else {
            // Live Credentials
            $this->ApplicationID = isset($DataArray['ApplicationID']) ? $DataArray['ApplicationID'] : '';
            $this->APIUsername = isset($DataArray['APIUsername']) && $DataArray['APIUsername'] != '' ? $DataArray['APIUsername'] : '';
            $this->APIPassword = isset($DataArray['APIPassword']) && $DataArray['APIPassword'] != '' ? $DataArray['APIPassword'] : '';
            $this->APISignature = isset($DataArray['APISignature']) && $DataArray['APISignature'] != '' ? $DataArray['APISignature'] : '';
            $this->EndPointURL = isset($DataArray['EndPointURL']) && $DataArray['EndPointURL'] != '' ? $DataArray['EndPointURL'] : 'https://svcs.paypal.com/';
        }
    }

    /**
     * Build all HTTP headers required for the API call.
     *
     * @access	public
     * @param	boolean	$PrintHeaders - Whether to print headers on screen or not (true/false)
     * @return	array $headers
     */

    /**
     * Builds HTTP headers for an API request.
     *
     * @access	public
     * @param	boolean	$PrintHeaders	Option to output headers to the screen (true/false).
     * @return	string	$headers		String of HTTP headers.	
     */
    function BuildHeaders($PrintHeaders, $APIName = "", $APIOperation = "") {
        if ($this->ThirdPartyPermission == TRUE) {

            $AuthObject = new \AuthSignature();
            $AuthResponse = $AuthObject->genSign($this->APIUsername, $this->APIPassword, $this->Token, $this->TokenSecret, 'POST', $this->EndPointURL . $APIName . '/' . $APIOperation);
            $AuthString = "token=" . $this->Token . ",signature=" . $AuthResponse['oauth_signature'] . ",timestamp=" . $AuthResponse['oauth_timestamp'];
            $AuthHeaderString = 'X-PAYPAL-AUTHORIZATION: ' . $AuthString;
        } else {
            $AuthHeaderString = '';
        }

        $headers = array(
            'X-PAYPAL-SECURITY-USERID: ' . $this->APIUsername,
            'X-PAYPAL-SECURITY-PASSWORD: ' . $this->APIPassword,
            'X-PAYPAL-SECURITY-SIGNATURE: ' . $this->APISignature,
            'X-PAYPAL-SECURITY-SUBJECT: ' . $this->APISubject,
            'X-PAYPAL-REQUEST-DATA-FORMAT: XML',
            'X-PAYPAL-RESPONSE-DATA-FORMAT: XML',
            'X-PAYPAL-APPLICATION-ID: ' . $this->ApplicationID,
            'X-PAYPAL-DEVICE-ID: ' . $this->DeviceID,
            'X-PAYPAL-DEVICE-IPADDRESS: ' . $this->IPAddress
        );

        if (!empty($AuthHeaderString)) {
            array_push($headers, $AuthHeaderString);
        }

        if ($this->Sandbox) {
            array_push($headers, 'X-PAYPAL-SANDBOX-EMAIL-ADDRESS: ' . $this->DeveloperAccountEmail);
        }

        if ($PrintHeaders) {
            echo '<pre />';
            print_r($headers);
        }

        return $headers;
    }

    /**
     * Send the API request to PayPal using CURL.
     *
     * @access	public
     * @param	string	$Request		Raw API request string.
     * @param	string	$APIName		The name of the API which you are calling.
     * @param	string	$APIOperation	The method in the API you're calling.
     * @param   string  $PrintHeaders   The option to print header output or not.
     * @return	string	$Response		Returns the raw HTTP response from PayPal.
     */
    function CURLRequest($Request = "", $APIName = "", $APIOperation = "", $PrintHeaders = false) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $this->EndPointURL . $APIName . '/' . $APIOperation);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $Request);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->BuildHeaders($this->PrintHeaders, $APIName, $APIOperation));

        if ($this->APIMode == 'Certificate') {
            curl_setopt($curl, CURLOPT_SSLCERT, $this->PathToCertKeyPEM);
        }

        $Response = curl_exec($curl);
        curl_close($curl);
        return $Response;
    }

    /**
     * Get all errors returned from PayPal
     *
     * @access	public
     * @param	string	$XML			XML response from PayPal
     * @return	mixed[]	$ErrorsArray	Returns a parsed array of PayPal errors/warnings.
     */
    function GetErrors($XML) {
        $DOM = new DOMDocument();
        $DOM->loadXML($XML);

        $Errors = $DOM->getElementsByTagName('error')->length > 0 ? $DOM->getElementsByTagName('error') : array();
        $ErrorsArray = array();
        foreach ($Errors as $Error) {
            $Receiver = $Error->getElementsByTagName('receiver')->length > 0 ? $Error->getElementsByTagName('receiver')->item(0)->nodeValue : '';
            $Category = $Error->getElementsByTagName('category')->length > 0 ? $Error->getElementsByTagName('category')->item(0)->nodeValue : '';
            $Domain = $Error->getElementsByTagName('domain')->length > 0 ? $Error->getElementsByTagName('domain')->item(0)->nodeValue : '';
            $ErrorID = $Error->getElementsByTagName('errorId')->length > 0 ? $Error->getElementsByTagName('errorId')->item(0)->nodeValue : '';
            $ExceptionID = $Error->getElementsByTagName('exceptionId')->length > 0 ? $Error->getElementsByTagName('exceptionId')->item(0)->nodeValue : '';
            $Message = $Error->getElementsByTagName('message')->length > 0 ? $Error->getElementsByTagName('message')->item(0)->nodeValue : '';
            $Parameter = $Error->getElementsByTagName('parameter')->length > 0 ? $Error->getElementsByTagName('parameter')->item(0)->nodeValue : '';
            $Severity = $Error->getElementsByTagName('severity')->length > 0 ? $Error->getElementsByTagName('severity')->item(0)->nodeValue : '';
            $Subdomain = $Error->getElementsByTagName('subdomain')->length > 0 ? $Error->getElementsByTagName('subdomain')->item(0)->nodeValue : '';

            $CurrentError = array(
                'Receiver' => $Receiver,
                'Category' => $Category,
                'Domain' => $Domain,
                'ErrorID' => $ErrorID,
                'ExceptionID' => $ExceptionID,
                'Message' => $Message,
                'Parameter' => $Parameter,
                'Severity' => $Severity,
                'Subdomain' => $Subdomain
            );
            array_push($ErrorsArray, $CurrentError);
        }
        return $ErrorsArray;
    }

    /**
     * Get the request envelope from the XML string
     *
     * @access	public
     * @return	string	$XML	Returns raw XML request envelope
     */
    function GetXMLRequestEnvelope() {
        $XML = '<requestEnvelope xmlns="">';
        $XML .= '<detailLevel>' . $this->DetailLevel . '</detailLevel>';
        $XML .= '<errorLanguage>' . $this->ErrorLanguage . '</errorLanguage>';
        $XML .= '</requestEnvelope>';

        return $XML;
    }

    /**
     * Log result to a location on the disk.
     *
     * @param $log_path
     * @param $filename
     * @param $string_data
     * @return bool
     */
    function Logger($log_path, $filename, $string_data) {

        if ($this->LogResults) {
            $timestamp = strtotime('now');
            $timestamp = date('mdY_gi_s_A_', $timestamp);

            $file = $log_path . $timestamp . $filename . '.xml';
            $fh = fopen($file, 'w');
            fwrite($fh, $string_data);
            fclose($fh);
        }

        return true;
    }

    /**
     * GetUserAgreement.
     *
     * Retrieves the user agreement for the customer to approve the new PayPal account.
     *
     * @access	public
     * @param	mixed[]	$DataArray			Array structure of PayPal request data.
     * @return	mixed[] $ResponseDataArray	Returns XML result parsed as an array.
     */
    function GetUserAgreement($DataArray) {
        $GetUserAgreementFields = isset($DataArray['GetUserAgreementFields']) ? $DataArray['GetUserAgreementFields'] : array();
        $CountryCode = isset($GetUserAgreementFields['CountryCode']) ? $GetUserAgreementFields['CountryCode'] : '';
        $CreateAccountKey = isset($GetUserAgreementFields['CreateAccountKey']) ? $GetUserAgreementFields['CreateAccountKey'] : '';
        $LanguageCode = isset($GetUserAgreementFields['LanguageCode']) ? $GetUserAgreementFields['LanguageCode'] : '';

        // Generate XML Request
        $XMLRequest = '<?xml version="1.0" encoding="utf-8"?>';
        $XMLRequest .= '<GetUserAgreementRequest xmlns="' . $this->XMLNamespace . '">';
        $XMLRequest .= $this->GetXMLRequestEnvelope();
        $XMLRequest .= $CountryCode != '' ? '<countryCode xmlns="">' . $CountryCode . '</countryCode>' : '';
        $XMLRequest .= $CreateAccountKey != '' ? '<createAccountKey xmlns="">' . $CreateAccountKey . '</createAccountKey>' : '';
        $XMLRequest .= $LanguageCode != '' ? '<languageCode xmlns="">' . $LanguageCode . '</languageCode>' : '';
        $XMLRequest .= '</GetUserAgreementRequest>';

        // Call the API and load XML response into DOM
        $XMLResponse = $this->CURLRequest($XMLRequest, 'AdaptiveAccounts', 'GetUserAgreement');
        $DOM = new DOMDocument();
        $DOM->loadXML($XMLResponse);

        $this->Logger($this->LogPath, __FUNCTION__ . 'Request', $XMLRequest);
        $this->Logger($this->LogPath, __FUNCTION__ . 'Response', $XMLResponse);

        // Parse XML values
        $Fault = $DOM->getElementsByTagName('FaultMessage')->length > 0 ? true : false;
        $Errors = $this->GetErrors($XMLResponse);
        $Ack = $DOM->getElementsByTagName('ack')->length > 0 ? $DOM->getElementsByTagName('ack')->item(0)->nodeValue : '';
        $Build = $DOM->getElementsByTagName('build')->length > 0 ? $DOM->getElementsByTagName('build')->item(0)->nodeValue : '';
        $CorrelationID = $DOM->getElementsByTagName('correlationId')->length > 0 ? $DOM->getElementsByTagName('correlationId')->item(0)->nodeValue : '';
        $Timestamp = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';

        $Agreement = $DOM->getElementsByTagName('agreement')->length > 0 ? $DOM->getElementsByTagName('agreement')->item(0)->nodeValue : '';

        $ResponseDataArray = array(
            'Errors' => $Errors,
            'Ack' => $Ack,
            'Build' => $Build,
            'CorrelationID' => $CorrelationID,
            'Timestamp' => $Timestamp,
            'Agreement' => $Agreement,
            'XMLRequest' => $XMLRequest,
            'XMLResponse' => $XMLResponse
        );

        return $ResponseDataArray;
    }

    /**
     * Submit RequestPermissions API request to PayPal.
     *
     * @access	public
     * @param	mixed[]	$DataArray			Array structure of PayPal request data.
     * @return	mixed[] $ResponseDataArray	Returns XML result parsed as an array.
     */
    function RequestPermissions($DataArray) {
        $RequestPermissionsFields = isset($DataArray['RequestPermissionsFields']) ? $DataArray['RequestPermissionsFields'] : array();
        $Scope = isset($RequestPermissionsFields['Scope']) ? $RequestPermissionsFields['Scope'] : array();
        $Callback = isset($RequestPermissionsFields['Callback']) ? $RequestPermissionsFields['Callback'] : '';

        // Generate XML Request
        $XMLRequest = '<?xml version="1.0" encoding="utf-8"?>';
        $XMLRequest .= '<RequestPermissionsRequest xmlns="' . $this->XMLNamespace . '">';
        $XMLRequest .= $this->GetXMLRequestEnvelope();

        foreach ($Scope as $Value) {
            $XMLRequest .= $Scope != '' ? '<scope xmlns="">' . $Value . '</scope>' : '';
        }

        $XMLRequest .= $Callback != '' ? '<callback xmlns="">' . $Callback . '</callback>' : '';
        $XMLRequest .= '</RequestPermissionsRequest>';

        // Call the API and load XML response into DOM
        $XMLResponse = $this->CURLRequest($XMLRequest, 'Permissions', 'RequestPermissions');
        $DOM = new DOMDocument();
        $DOM->loadXML($XMLResponse);

        $this->Logger($this->LogPath, __FUNCTION__ . 'Request', $XMLRequest);
        $this->Logger($this->LogPath, __FUNCTION__ . 'Response', $XMLResponse);

        // Parse XML values
        $Fault = $DOM->getElementsByTagName('FaultMessage')->length > 0 ? true : false;
        $Errors = $this->GetErrors($XMLResponse);
        $Ack = $DOM->getElementsByTagName('ack')->length > 0 ? $DOM->getElementsByTagName('ack')->item(0)->nodeValue : '';
        $Build = $DOM->getElementsByTagName('build')->length > 0 ? $DOM->getElementsByTagName('build')->item(0)->nodeValue : '';
        $CorrelationID = $DOM->getElementsByTagName('correlationId')->length > 0 ? $DOM->getElementsByTagName('correlationId')->item(0)->nodeValue : '';
        $Timestamp = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';

        $Token = $DOM->getElementsByTagName('token')->length > 0 ? $DOM->getElementsByTagName('token')->item(0)->nodeValue : '';
        $RedirectURL = $this->Sandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_grant-permission&request_token=' . $Token : 'https://www.paypal.com/cgi-bin/webscr?cmd=_grant-permission&request_token=' . $Token;

        $ResponseDataArray = array(
            'Errors' => $Errors,
            'Ack' => $Ack,
            'Build' => $Build,
            'CorrelationID' => $CorrelationID,
            'Timestamp' => $Timestamp,
            'Token' => $Token,
            'RedirectURL' => $RedirectURL,
            'XMLRequest' => $XMLRequest,
            'XMLResponse' => $XMLResponse
        );

        return $ResponseDataArray;
    }

    /**
     * Submit GetAccessToken API request to PayPal.
     *
     * @access	public
     * @param	mixed[]	$DataArray			Array structure of PayPal request data.
     * @return	mixed[] $ResponseDataArray	Returns XML result parsed as an array.
     */
    function GetAccessToken($DataArray) {
        $GetAccessTokenFields = isset($DataArray['GetAccessTokenFields']) ? $DataArray['GetAccessTokenFields'] : array();
        $Token = isset($GetAccessTokenFields['Token']) ? $GetAccessTokenFields['Token'] : '';
        $Verifier = isset($GetAccessTokenFields['Verifier']) ? $GetAccessTokenFields['Verifier'] : '';
        $SubjectAlias = isset($GetAccessTokenFields['SubjectAlias']) ? $GetAccessTokenFields['SubjectAlias'] : '';

        // Generate XML Request
        $XMLRequest = '<?xml version="1.0" encoding="utf-8"?>';
        $XMLRequest .= '<GetAccessTokenRequest xmlns="' . $this->XMLNamespace . '">';
        $XMLRequest .= $this->GetXMLRequestEnvelope();
        $XMLRequest .= $Token != '' ? '<token xmlns="">' . $Token . '</token>' : '';
        $XMLRequest .= $Verifier != '' ? '<verifier xmlns="">' . $Verifier . '</verifier>' : '';
        $XMLRequest .= $SubjectAlias != '' ? '<subjectAlias xmlns="">' . $SubjectAlias . '</subjectAlias>' : '';
        $XMLRequest .= '</GetAccessTokenRequest>';

        // Call the API and load XML response into DOM
        $XMLResponse = $this->CURLRequest($XMLRequest, 'Permissions', 'GetAccessToken');
        $DOM = new DOMDocument();
        $DOM->loadXML($XMLResponse);

        $this->Logger($this->LogPath, __FUNCTION__ . 'Request', $XMLRequest);
        $this->Logger($this->LogPath, __FUNCTION__ . 'Response', $XMLResponse);

        // Parse XML values
        $Fault = $DOM->getElementsByTagName('FaultMessage')->length > 0 ? true : false;
        $Errors = $this->GetErrors($XMLResponse);
        $Ack = $DOM->getElementsByTagName('ack')->length > 0 ? $DOM->getElementsByTagName('ack')->item(0)->nodeValue : '';
        $Build = $DOM->getElementsByTagName('build')->length > 0 ? $DOM->getElementsByTagName('build')->item(0)->nodeValue : '';
        $CorrelationID = $DOM->getElementsByTagName('correlationId')->length > 0 ? $DOM->getElementsByTagName('correlationId')->item(0)->nodeValue : '';
        $Timestamp = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';

        $Scope = array();
        $Scopes = $DOM->getElementsByTagName('scope')->length > 0 ? $DOM->getElementsByTagName('scope') : array();
        foreach ($Scopes as $ScopeType) {
            array_push($Scope, $ScopeType->nodeValue);
        }

        $Token = $DOM->getElementsByTagName('token')->length > 0 ? $DOM->getElementsByTagName('token')->item(0)->nodeValue : '';
        $TokenSecret = $DOM->getElementsByTagName('tokenSecret')->length > 0 ? $DOM->getElementsByTagName('tokenSecret')->item(0)->nodeValue : '';

        $ResponseDataArray = array(
            'Errors' => $Errors,
            'Ack' => $Ack,
            'Build' => $Build,
            'CorrelationID' => $CorrelationID,
            'Timestamp' => $Timestamp,
            'Scope' => $Scope,
            'Token' => $Token,
            'TokenSecret' => $TokenSecret,
            'XMLRequest' => $XMLRequest,
            'XMLResponse' => $XMLResponse
        );

        return $ResponseDataArray;
    }

    /**
     * Submit GetPermissions API request to PayPal.
     *
     * @access	public
     * @param	string	$Token				Token returned from a GetAccessToken request.
     * @return	mixed[] $ResponseDataArray	Returns XML result parsed as an array.
     */
    function GetPermissions($Token) {
        // Generate XML Request
        $XMLRequest = '<?xml version="1.0" encoding="utf-8"?>';
        $XMLRequest .= '<GetPermissionsRequest xmlns="' . $this->XMLNamespace . '">';
        $XMLRequest .= $this->GetXMLRequestEnvelope();
        $XMLRequest .= $Token != '' ? '<token xmlns="">' . $Token . '</token>' : '';
        $XMLRequest .= '</GetPermissionsRequest>';

        // Call the API and load XML response into DOM
        $XMLResponse = $this->CURLRequest($XMLRequest, 'Permissions', 'GetPermissions');
        $DOM = new DOMDocument();
        $DOM->loadXML($XMLResponse);

        $this->Logger($this->LogPath, __FUNCTION__ . 'Request', $XMLRequest);
        $this->Logger($this->LogPath, __FUNCTION__ . 'Response', $XMLResponse);

        // Parse XML values
        $Fault = $DOM->getElementsByTagName('FaultMessage')->length > 0 ? true : false;
        $Errors = $this->GetErrors($XMLResponse);
        $Ack = $DOM->getElementsByTagName('ack')->length > 0 ? $DOM->getElementsByTagName('ack')->item(0)->nodeValue : '';
        $Build = $DOM->getElementsByTagName('build')->length > 0 ? $DOM->getElementsByTagName('build')->item(0)->nodeValue : '';
        $CorrelationID = $DOM->getElementsByTagName('correlationId')->length > 0 ? $DOM->getElementsByTagName('correlationId')->item(0)->nodeValue : '';
        $Timestamp = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';

        $Scope = array();
        $Scopes = $DOM->getElementsByTagName('scope')->length > 0 ? $DOM->getElementsByTagName('scope') : array();
        foreach ($Scopes as $ScopeType) {
            array_push($Scope, $ScopeType->nodeValue);
        }


        $ResponseDataArray = array(
            'Errors' => $Errors,
            'Ack' => $Ack,
            'Build' => $Build,
            'CorrelationID' => $CorrelationID,
            'Timestamp' => $Timestamp,
            'Scope' => $Scope,
            'XMLRequest' => $XMLRequest,
            'XMLResponse' => $XMLResponse
        );

        return $ResponseDataArray;
    }

    /**
     * Submit CancelPermissions API request to PayPal.
     *
     * @access	public
     * @param	string	$Token				Token returned from a GetAccessToken request.
     * @return	mixed[] $ResponseDataArray	Returns XML result parsed as an array.
     */
    function CancelPermissions($Token) {
        // Generate XML Request
        $XMLRequest = '<?xml version="1.0" encoding="utf-8"?>';
        $XMLRequest .= '<CancelPermissionsRequest xmlns="' . $this->XMLNamespace . '">';
        $XMLRequest .= $this->GetXMLRequestEnvelope();
        $XMLRequest .= $Token != '' ? '<token xmlns="">' . $Token . '</token>' : '';
        $XMLRequest .= '</CancelPermissionsRequest>';

        // Call the API and load XML response into DOM
        $XMLResponse = $this->CURLRequest($XMLRequest, 'Permissions', 'CancelPermissions');
        $DOM = new DOMDocument();
        $DOM->loadXML($XMLResponse);

        $this->Logger($this->LogPath, __FUNCTION__ . 'Request', $XMLRequest);
        $this->Logger($this->LogPath, __FUNCTION__ . 'Response', $XMLResponse);

        // Parse XML values
        $Fault = $DOM->getElementsByTagName('FaultMessage')->length > 0 ? true : false;
        $Errors = $this->GetErrors($XMLResponse);
        $Ack = $DOM->getElementsByTagName('ack')->length > 0 ? $DOM->getElementsByTagName('ack')->item(0)->nodeValue : '';
        $Build = $DOM->getElementsByTagName('build')->length > 0 ? $DOM->getElementsByTagName('build')->item(0)->nodeValue : '';
        $CorrelationID = $DOM->getElementsByTagName('correlationId')->length > 0 ? $DOM->getElementsByTagName('correlationId')->item(0)->nodeValue : '';
        $Timestamp = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';

        $ResponseDataArray = array(
            'Errors' => $Errors,
            'Ack' => $Ack,
            'Build' => $Build,
            'CorrelationID' => $CorrelationID,
            'Timestamp' => $Timestamp,
            'XMLRequest' => $XMLRequest,
            'XMLResponse' => $XMLResponse
        );

        return $ResponseDataArray;
    }

    /**
     * Submit GetBasicPersonalData API request to PayPal.
     *
     * @access	public
     * @param	mixed[]	$AttributeList		Array structure of the list of attributes to obtain for a user.
     * @return	mixed[] $ResponseDataArray	Returns XML result parsed as an array.
     */
    function GetBasicPersonalData($AttributeList) {
        // Generate XML Request
        $XMLRequest = '<?xml version="1.0" encoding="utf-8"?>';
        $XMLRequest .= '<GetBasicPersonalDataRequest xmlns="' . $this->XMLNamespace . '">';
        $XMLRequest .= $this->GetXMLRequestEnvelope();
        $XMLRequest .= '<attributeList xmlns="">';
        foreach ($AttributeList as $Attribute) {
            $XMLRequest .= '<attribute xmlns="">' . $Attribute . '</attribute>';
        }
        $XMLRequest .= '</attributeList>';
        $XMLRequest .= '</GetBasicPersonalDataRequest>';

        // Call the API and load XML response into DOM
        $XMLResponse = $this->CURLRequest($XMLRequest, 'Permissions', 'GetBasicPersonalData');
        $DOM = new DOMDocument();
        $DOM->loadXML($XMLResponse);

        $this->Logger($this->LogPath, __FUNCTION__ . 'Request', $XMLRequest);
        $this->Logger($this->LogPath, __FUNCTION__ . 'Response', $XMLResponse);

        // Parse XML values
        $Fault = $DOM->getElementsByTagName('FaultMessage')->length > 0 ? true : false;
        $Errors = $this->GetErrors($XMLResponse);
        $Ack = $DOM->getElementsByTagName('ack')->length > 0 ? $DOM->getElementsByTagName('ack')->item(0)->nodeValue : '';
        $Build = $DOM->getElementsByTagName('build')->length > 0 ? $DOM->getElementsByTagName('build')->item(0)->nodeValue : '';
        $CorrelationID = $DOM->getElementsByTagName('correlationId')->length > 0 ? $DOM->getElementsByTagName('correlationId')->item(0)->nodeValue : '';
        $Timestamp = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';

        $PersonalDataList = $DOM->getElementsByTagName('personalData')->length > 0 ? $DOM->getElementsByTagName('personalData') : array();

        $PersonalData = array();
        foreach ($PersonalDataList as $PersonalDataType) {
            $PersonalDataKey = $PersonalDataType->getElementsByTagName('personalDataKey')->length > 0 ? $PersonalDataType->getElementsByTagName('personalDataKey')->item(0)->nodeValue : '';
            $PersonalDataValue = $PersonalDataType->getElementsByTagName('personalDataValue')->length > 0 ? $PersonalDataType->getElementsByTagName('personalDataValue')->item(0)->nodeValue : '';

            $PersonalDataItem = array('PersonalDataKey' => $PersonalDataKey, 'PersonalDataValue' => $PersonalDataValue);
            array_push($PersonalData, $PersonalDataItem);
        }

        $PersonalDataKey = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';
        $PersonalDataValue = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';

        $ResponseDataArray = array(
            'Errors' => $Errors,
            'Ack' => $Ack,
            'Build' => $Build,
            'CorrelationID' => $CorrelationID,
            'Timestamp' => $Timestamp,
            'PersonalData' => $PersonalData,
            'XMLRequest' => $XMLRequest,
            'XMLResponse' => $XMLResponse
        );

        return $ResponseDataArray;
    }

    /**
     * Submit GetAdvancedPersonalData API request to PayPal.
     *
     * @access	public
     * @param	mixed[]	$AttributeList		Array structure of the list of attributes to obtain for a user.
     * @return	mixed[] $ResponseDataArray	Returns XML result parsed as an array.
     */
    function GetAdvancedPersonalData($AttributeList) {
        // Generate XML Request
        $XMLRequest = '<?xml version="1.0" encoding="utf-8"?>';
        $XMLRequest .= '<GetAdvancedPersonalDataRequest xmlns="' . $this->XMLNamespace . '">';
        $XMLRequest .= $this->GetXMLRequestEnvelope();
        $XMLRequest .= '<attributeList xmlns="">';
        foreach ($AttributeList as $Attribute) {
            $XMLRequest .= '<attribute xmlns="">' . $Attribute . '</attribute>';
        }
        $XMLRequest .= '</attributeList>';
        $XMLRequest .= '</GetAdvancedPersonalDataRequest>';

        // Call the API and load XML response into DOM
        $XMLResponse = $this->CURLRequest($XMLRequest, 'Permissions', 'GetAdvancedPersonalData');
        $DOM = new DOMDocument();
        $DOM->loadXML($XMLResponse);

        $this->Logger($this->LogPath, __FUNCTION__ . 'Request', $XMLRequest);
        $this->Logger($this->LogPath, __FUNCTION__ . 'Response', $XMLResponse);

        // Parse XML values
        $Fault = $DOM->getElementsByTagName('FaultMessage')->length > 0 ? true : false;
        $Errors = $this->GetErrors($XMLResponse);
        $Ack = $DOM->getElementsByTagName('ack')->length > 0 ? $DOM->getElementsByTagName('ack')->item(0)->nodeValue : '';
        $Build = $DOM->getElementsByTagName('build')->length > 0 ? $DOM->getElementsByTagName('build')->item(0)->nodeValue : '';
        $CorrelationID = $DOM->getElementsByTagName('correlationId')->length > 0 ? $DOM->getElementsByTagName('correlationId')->item(0)->nodeValue : '';
        $Timestamp = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';

        $PersonalDataList = $DOM->getElementsByTagName('personalData')->length > 0 ? $DOM->getElementsByTagName('personalData') : array();

        $PersonalData = array();
        foreach ($PersonalDataList as $PersonalDataType) {
            $PersonalDataKey = $PersonalDataType->getElementsByTagName('personalDataKey')->length > 0 ? $PersonalDataType->getElementsByTagName('personalDataKey')->item(0)->nodeValue : '';
            $PersonalDataValue = $PersonalDataType->getElementsByTagName('personalDataValue')->length > 0 ? $PersonalDataType->getElementsByTagName('personalDataValue')->item(0)->nodeValue : '';

            $PersonalDataItem = array('PersonalDataKey' => $PersonalDataKey, 'PersonalDataValue' => $PersonalDataValue);
            array_push($PersonalData, $PersonalDataItem);
        }

        $PersonalDataKey = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';
        $PersonalDataValue = $DOM->getElementsByTagName('timestamp')->length > 0 ? $DOM->getElementsByTagName('timestamp')->item(0)->nodeValue : '';

        $ResponseDataArray = array(
            'Errors' => $Errors,
            'Ack' => $Ack,
            'Build' => $Build,
            'CorrelationID' => $CorrelationID,
            'Timestamp' => $Timestamp,
            'PersonalData' => $PersonalData,
            'XMLRequest' => $XMLRequest,
            'XMLResponse' => $XMLResponse
        );

        return $ResponseDataArray;
    }

}

// End Class PayPal_Adaptive
