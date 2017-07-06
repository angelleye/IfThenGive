<?php 

/**
 * Primary PayPal Class
 *
 * This is the parent PayPal class that all child classes extend.  
 *
 * @package 		paypal-php-library
 * @author			Andrew Angell <service@angelleye.com>
 */
class GiveWhen_Angelleye_PayPal
{

	var $APIUsername = '';
	var $APIPassword = '';
	var $APISignature = '';
	var $APISubject = '';
	var $APIVersion = '';
	var $APIButtonSource = '';
	var $APIMode = '';
	var $EndPointURL = '';
	var $Sandbox = '';
	var $PathToCertKeyPEM = '';
	var $SSL = '';
    var $PrintHeaders = '';
    var $LogResults = '';
    var $LogPath = '';
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	Array structure providing config data
	 * @return	void
	 */
	function __construct($DataArray)
	{		
		if(isset($DataArray['Sandbox']))
		{
			$this->Sandbox = $DataArray['Sandbox'];
		
		}
		else
		{
			$this->Sandbox = true;
		}
			
		$this->APIVersion = isset($DataArray['APIVersion']) ? $DataArray['APIVersion'] : '119.0';
		$this->APIMode = isset($DataArray['APIMode']) ? $DataArray['APIMode'] : 'Signature';
		$this->APIButtonSource = 'AngellEYE_GiveWhen';
		$this->PathToCertKeyPEM = '/path/to/cert/pem.txt';
		$this->SSL = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? true : false;
		$this->APISubject = isset($DataArray['APISubject']) ? $DataArray['APISubject'] : '';
        $this->PrintHeaders = isset($DataArray['PrintHeaders']) ? $DataArray['PrintHeaders'] : false;
        $this->LogResults = isset($DataArray['LogResults']) ? $DataArray['LogResults'] : false;
        $this->LogPath = isset($DataArray['LogPath']) ? $DataArray['LogPath'] : '/logs/';

		if($this->Sandbox)
		{
			// Show Errors
			error_reporting(E_ALL);
			ini_set('display_errors', '1');
		
			# Sandbox
			$this->APIUsername = isset($DataArray['APIUsername']) && $DataArray['APIUsername'] != '' ? $DataArray['APIUsername'] : '';
			$this->APIPassword = isset($DataArray['APIPassword']) && $DataArray['APIPassword'] != '' ? $DataArray['APIPassword'] : '';
			$this->APISignature = isset($DataArray['APISignature']) && $DataArray['APISignature'] != '' ? $DataArray['APISignature'] : '';
			$this->EndPointURL = isset($DataArray['EndPointURL']) && $DataArray['EndPointURL'] != '' ? $DataArray['EndPointURL'] : 'https://api-3t.sandbox.paypal.com/nvp';	
		}
		else
		{
			$this->APIUsername = isset($DataArray['APIUsername']) && $DataArray['APIUsername'] != '' ? $DataArray['APIUsername'] : '';
			$this->APIPassword = isset($DataArray['APIPassword']) && $DataArray['APIPassword'] != '' ? $DataArray['APIPassword'] : '';
			$this->APISignature = isset($DataArray['APISignature']) && $DataArray['APISignature'] != '' ? $DataArray['APISignature'] : '';
			$this->EndPointURL = isset($DataArray['EndPointURL']) && $DataArray['EndPointURL'] != ''  ? $DataArray['EndPointURL'] : 'https://api-3t.paypal.com/nvp';
		}
				
		// Create the NVP credentials string which is required in all calls.
		$this->NVPCredentials = 'USER=' . $this->APIUsername . '&PWD=' . $this->APIPassword . '&VERSION=' . $this->APIVersion . '&BUTTONSOURCE=' . $this->APIButtonSource;
		$this->NVPCredentials .= $this->APISubject != '' ? '&SUBJECT=' . $this->APISubject : '';
		$this->NVPCredentials .= $this->APIMode == 'Signature' ? '&SIGNATURE=' . $this->APISignature : '';
		
		$this->Countries = array(
							'Afghanistan' => 'AF',
							'ÌÉland Islands' => 'AX',
							'Albania' => 'AL',
							'Algeria' => 'DZ',
							'American Samoa' => 'AS',
							'Andorra' => 'AD',
							'Angola' => 'AO',
							'Anguilla' => 'AI',
							'Antarctica' => 'AQ',
							'Antigua and Barbuda' => 'AG',
							'Argentina' => 'AR',
							'Armenia' => 'AM',
							'Aruba' => 'AW',
							'Australia' => 'AU',
							'Austria' => 'AT',
							'Azerbaijan' => 'AZ',
							'Bahamas' => 'BS',
							'Bahrain' => 'BH',
							'Bangladesh' => 'BD',
							'Barbados' => 'BB',
							'Belarus' => 'BY',
							'Belgium' => 'BE',
							'Belize' => 'BZ',
							'Benin' => 'BJ',
							'Bermuda' => 'BM',
							'Bhutan' => 'BT',
							'Bolivia' => 'BO',
							'Bosnia and Herzegovina' => 'BA',
							'Botswana' => 'BW',
							'Bouvet Island' => 'BV',
							'Brazil' => 'BR',
							'British Indian Ocean Territory' => 'IO',
							'Brunei Darussalam' => 'BN',
							'Bulgaria' => 'BG',
							'Burkina Faso' => 'BF',
							'Burundi' => 'BI',
							'Cambodia' => 'KH',
							'Cameroon' => 'CM',
							'Canada' => 'CA',
							'Cape Verde' => 'CV',
							'Cayman Islands' => 'KY',
							'Central African Republic' => 'CF',
							'Chad' => 'TD',
							'Chile' => 'CL',
							'China' => 'CN',
							'Christmas Island' => 'CX',
							'Cocos (Keeling) Islands' => 'CC',
							'Colombia' => 'CO',
							'Comoros' => 'KM',
							'Congo' => 'CG',
							'Congo, The Democratic Republic of the' => 'CD',
							'Cook Islands' => 'CK',
							'Costa Rica' => 'CR',
							"Cote D'Ivoire" => 'CI',
							'Croatia' => 'HR',
							'Cuba' => 'CU',
							'Cyprus' => 'CY',
							'Czech Republic' => 'CZ',
							'Denmark' => 'DK',
							'Djibouti' => 'DJ',
							'Dominica' => 'DM',
							'Dominican Republic' => 'DO',
							'Ecuador' => 'EC',
							'Egypt' => 'EG',
							'El Salvador' => 'SV',
							'Equatorial Guinea' => 'GQ',
							'Eritrea' => 'ER',
							'Estonia' => 'EE',
							'Ethiopia' => 'ET',
							'Falkland Islands (Malvinas)' => 'FK',
							'Faroe Islands' => 'FO',
							'Fiji' => 'FJ',
							'Finland' => 'FI',
							'France' => 'FR',
							'French Guiana' => 'GF',
							'French Polynesia' => 'PF',
							'French Southern Territories' => 'TF',
							'Gabon' => 'GA',
							'Gambia' => 'GM',
							'Georgia' => 'GE',
							'Germany' => 'DE',
							'Ghana' => 'GH',
							'Gibraltar' => 'GI',
							'Greece' => 'GR',
							'Greenland' => 'GL',
							'Grenada' => 'GD',
							'Guadeloupe' => 'GP',
							'Guam' => 'GU',
							'Guatemala' => 'GT',
							'Guernsey' => 'GG',
							'Guinea' => 'GN',
							'Guinea-Bissau' => 'GW',
							'Guyana' => 'GY',
							'Haiti' => 'HT',
							'Heard Island and McDonald Islands' => 'HM',
							'Holy See (Vatican City State)' => 'VA',
							'Honduras' => 'HN',
							'Hong Kong' => 'HK',
							'Hungary' => 'HU',
							'Iceland' => 'IS',
							'India' => 'IN',
							'Indonesia' => 'ID',
							'Iran, Islamic Republic of' => 'IR',
							'Iraq' => 'IQ',
							'Ireland' => 'IE',
							'Isle of Man' => 'IM',
							'Israel' => 'IL',
							'Italy' => 'IT',
							'Jamaica' => 'JM',
							'Japan' => 'JP',
							'Jersey' => 'JE',
							'Jordan' => 'JO',
							'Kazakhstan' => 'KZ',
							'Kenya' => 'KE',
							'Kiribati' => 'KI',
							"Korea, Democratic People's Republic of" => 'KP',
							'Korea, Republic of' => 'KR',
							'Kuwait' => 'KW',
							'Kyrgyzstan' => 'KG',
							"Laos People's Democratic Republic" => 'LA',
							'Latvia' => 'LV',
							'Lebanon' => 'LB',
							'Lesotho' => 'LS',
							'Liberia' => 'LR',
							'Libyan Arab Jamahiriya' => 'LY',
							'Liechtenstein' => 'LI',
							'Lithuania' => 'LT',
							'Luxembourg' => 'LU',
							'Macao' => 'MO',
							'Macedonia, The former Yugoslav Republic of' => 'MK',
							'Madagascar' => 'MG',
							'Malawi' => 'MW',
							'Malaysia' => 'MY',
							'Maldives' => 'MV',
							'Mali' => 'ML',
							'Malta' => 'MT',
							'Marshall Islands' => 'MH',
							'Martinique' => 'MQ',
							'Mauritania' => 'MR',
							'Mauritius' => 'MU',
							'Mayotte' => 'YT',
							'Mexico' => 'MX',
							'Micronesia, Federated States of' => 'FM',
							'Moldova, Republic of' => 'MD',
							'Monaco' => 'MC',
							'Mongolia' => 'MN',
							'Montserrat' => 'MS',
							'Morocco' => 'MA',
							'Mozambique' => 'MZ',
							'Myanmar' => 'MM',
							'Namibia' => 'NA',
							'Nauru' => 'NR',
							'Nepal' => 'NP',
							'Netherlands' => 'NL',
							'Netherlands Antilles' => 'AN',
							'New Caledonia' => 'NC',
							'New Zealand' => 'NZ',
							'Nicaragua' => 'NI',
							'Niger' => 'NE',
							'Nigeria' => 'NG',
							'Niue' => 'NU',
							'Norfolk Island' => 'NF',
							'Northern Mariana Islands' => 'MP',
							'Norway' => 'NO',
							'Oman' => 'OM',
							'Pakistan' => 'PK',
							'Palau' => 'PW',
							'Palestinian Territory, Occupied' => 'PS',
							'Panama' => 'PA',
							'Papua New Guinea' => 'PG',
							'Paraguay' => 'PY',
							'Peru' => 'PE',
							'Philippines' => 'PH',
							'Pitcairn' => 'PN',
							'Poland' => 'PL',
							'Portugal' => 'PT',
							'Puerto Rico' => 'PR',
							'Qatar' => 'QA',
							'Reunion' => 'RE',
							'Romania' => 'RO',
							'Russian Federation' => 'RU',
							'Rwanda' => 'RW',
							'Saint Helena' => 'SH',
							'Saint Kitts and Nevis' => 'KN',
							'Saint Lucia' => 'LC',
							'Saint Pierre and Miquelon' => 'PM',
							'Saint Vincent and the Grenadines' => 'VC',
							'Samoa' => 'WS',
							'San Marino' => 'SM',
							'Sao Tome and Principe' => 'ST',
							'Saudi Arabia' => 'SA',
							'Senegal' => 'SN',
							'Serbia and Montenegro' => 'CS',
							'Seychelles' => 'SC',
							'Sierra Leone' => 'SL',
							'Singapore' => 'SG',
							'Slovakia' => 'SK',
							'Slovenia' => 'SI',
							'Solomon Islands' => 'SB',
							'Somalia' => 'SO',
							'South Africa' => 'ZA',
							'South Georgia and the South Sandwich Islands' => 'GS',
							'Spain' => 'ES',
							'Sri Lanka' => 'LK',
							'Sudan' => 'SD',
							'Suriname' => 'SR',
							'SValbard and Jan Mayen' => 'SJ',
							'Swaziland' => 'SZ',
							'Sweden' => 'SE',
							'Switzerland' => 'CH',
							'Syrian Arab Republic' => 'SY',
							'Taiwan, Province of China' => 'TW',
							'Tajikistan' => 'TJ',
							'Tanzania, United Republic of' => 'TZ',
							'Thailand' => 'TH',
							'Timor-Leste' => 'TL',
							'Togo' => 'TG',
							'Tokelau' => 'TK',
							'Tonga' => 'TO',
							'Trinidad and Tobago' => 'TT',
							'Tunisia' => 'TN',
							'Turkey' => 'TR',
							'Turkmenistan' => 'TM',
							'Turks and Caicos Islands' => 'TC',
							'Tuvalu' => 'TV',
							'Uganda' => 'UG',
							'Ukraine' => 'UA',
							'United Arab Emirates' => 'AE',
							'United Kingdom' => 'GB',
							'United States' => 'US',
							'United States Minor Outlying Islands' => 'UM',
							'Uruguay' => 'UY',
							'Uzbekistan' => 'UZ',
							'Vanuatu' => 'VU',
							'Venezuela' => 'VE',
							'Viet Nam' => 'VN',
							'Virgin Islands, British' => 'VG',
							'Virgin Islands, U.S.' => 'VI',
							'Wallis and Futuna' => 'WF',
							'Western Sahara' => 'EH',
							'Yemen' => 'YE',
							'Zambia' => 'ZM',
							'Zimbabwe' => 'ZW');
							
		$this->States = array(
						'Alberta' => 'AB',
						'British Columbia' => 'BC',
						'Manitoba' => 'MB',
						'New Brunswick' => 'NB',
						'Newfoundland and Labrador' => 'NF',
						'Northwest Territories' => 'NT',
						'Nova Scotia' => 'NS',
						'Nunavut' => 'NU',
						'Ontario' => 'ON',
						'Prince Edward Island' => 'PE',
						'Quebec' => 'QC',
						'Saskatchewan' => 'SK',
						'Yukon' => 'YK',
						'Alabama' => 'AL',
						'Alaska' => 'AK',
						'American Samoa' => 'AS',
						'Arizona' => 'AZ',
						'Arkansas' => 'AR',
						'California' => 'CA',
						'Colorado' => 'CO',
						'Connecticut' => 'CT',
						'Delaware' => 'DE',
						'District of Columbia' => 'DC',
						'Federated States of Micronesia' => 'FM',
						'Florida' => 'FL',
						'Georgia' => 'GA',
						'Guam' => 'GU',
						'Hawaii' => 'HI',
						'Idaho' => 'ID',
						'Illinois' => 'IL',
						'Indiana' => 'IN',
						'Iowa' => 'IA',
						'Kansas' => 'KS',
						'Kentucky' => 'KY',
						'Louisiana' => 'LA',
						'Maine' => 'ME',
						'Marshall Islands' => 'MH',
						'Maryland' => 'MD',
						'Massachusetts' => 'MA',
						'Michigan' => 'MI',
						'Minnesota' => 'MN',
						'Mississippi' => 'MS',
						'Missouri' => 'MO',
						'Montana' => 'MT',
						'Nebraska' => 'NE',
						'Nevada' => 'NV',
						'New Hampshire' => 'NH',
						'New Jersey' => 'NJ',
						'New Mexico' => 'NM',
						'New York' => 'NY',
						'North Carolina' => 'NC',
						'North Dakota' => 'ND',
						'Northern Mariana Islands' => 'MP',
						'Ohio' => 'OH',
						'Oklahoma' => 'OK',
						'Oregon' => 'OR',
						'Palau' => 'PW',
						'Pennsylvania' => 'PA',
						'Puerto Rico' => 'PR',
						'Rhode Island' => 'RI',
						'South Carolina' => 'SC',
						'South Dakota' => 'SD',
						'Tennessee' => 'TN',
						'Texas' => 'TX',
						'Utah' => 'UT',
						'Vermont' => 'VT',
						'Virgin Islands' => 'VI',
						'Virginia' => 'VA',
						'Washington' => 'WA',
						'West Virginia' => 'WV',
						'Wisconsin' => 'WI',
						'Wyoming' => 'WY',
						'Armed Forces Americas' => 'AA',
						'Armed Forces' => 'AE',
						'Armed Forces Pacific' => 'AP');
						
		$this->AVSCodes = array("A" => "Address Matches Only (No ZIP)", 
								  "B" => "Address Matches Only (No ZIP)", 
								  "C" => "This tranaction was declined.", 
								  "D" => "Address and Postal Code Match", 
								  "E" => "This transaction was declined.", 
								  "F" => "Address and Postal Code Match", 
								  "G" => "Global Unavailable - N/A", 
								  "I" => "International Unavailable - N/A", 
								  "N" => "None - Transaction was declined.", 
								  "P" => "Postal Code Match Only (No Address)", 
								  "R" => "Retry - N/A", 
								  "S" => "Service not supported - N/A", 
								  "U" => "Unavailable - N/A", 
								  "W" => "Nine-Digit ZIP Code Match (No Address)", 
								  "X" => "Exact Match - Address and Nine-Digit ZIP", 
								  "Y" => "Address and five-digit Zip match", 
								  "Z" => "Five-Digit ZIP Matches (No Address)");
								  
		$this->CVV2Codes = array(
									"E" => "N/A", 
								   	"M" => "Match", 
								   	"N" => "No Match", 
								   	"P" => "Not Processed - N/A", 
								   	"S" => "Service Not Supported - N/A", 
								   	"U" => "Service Unavailable - N/A", 
								   	"X" => "No Response - N/A"
									);
								   
		$this->CurrencyCodes = array(
										'AUD' => 'Austrailian Dollar', 
										'BRL' => 'Brazilian Real', 
										'CAD' => 'Canadian Dollar', 
										'CZK' => 'Czeck Koruna', 
										'DKK' => 'Danish Krone', 
										'EUR' => 'Euro', 
										'HKD' => 'Hong Kong Dollar', 
										'HUF' => 'Hungarian Forint', 
										'ILS' => 'Israeli New Sheqel', 
										'JPY' => 'Japanese Yen', 
										'MYR' => 'Malaysian Ringgit', 
										'MXN' => 'Mexican Peso', 
										'NOK' => 'Norwegian Krone', 
										'NZD' => 'New Zealand Dollar', 
										'PHP' => 'Philippine Peso', 
										'PLN' => 'Polish Zloty', 
										'GBP' => 'Pound Sterling', 
										'SGD' => 'Singapore Dollar', 
										'SEK' => 'Swedish Krona', 
										'CHF' => 'Swiss Franc', 
										'TWD' => 'Taiwan New Dollar', 
										'THB' => 'Thai Baht', 
										'USD' => 'U.S. Dollar'
										);
		
	
	}  // End function PayPalPro()
	
	/**
	 * Get the current API version setting.
	 *
	 * @access	public
	 * @return	string	Returns the current value for API version.
	 */
	function GetAPIVersion()
	{
		return $this->APIVersion;	
	}
	
	/**
	 * Get the country code of the requested country.
	 *
	 * @access	public
	 * @param	string	$CountryName	Name of the country to return the code for.
	 * @return	string	Returns the country code for the supplied country name.
	 */
	function GetCountryCode($CountryName)
	{
		return $this->Countries[$CountryName];
	}
	
	/**
	 * Get the state code for a requestad state.
	 *
	 * @access	public
	 * @param	string	$StateOrProvinceName	Name of the state or province to return the code for.
	 * @return	string	Returns the 2-letter code for the supplied state or province name.
	 */
	function GetStateCode($StateOrProvinceName)
	{
		return $this->States[$StateOrProvinceName];
	}
	
	/**
	 * Get the country name based on the country code.
	 *
	 * @access	public
	 * @param	string	$CountryCode	Country code that you would like the full name for.
	 * @return	string	Returns the country name for the supplied country code.
	 */
	function GetCountryName($CountryCode)
	{
		$Countries = array_flip($this->Countries);
		return $Countries[$CountryCode];
	}
	
	/**
	 * Get the state name based on the 2-letter code.
	 *
	 * @access	public
	 * @param	mixed[]	$StateOrProvinceName	State or province name that you would like the full name for.
	 * @return	string	Returns the full name of the state or province for the supplied code.
	 */
	function GetStateName($StateOrProvinceName)
	{
		$States = array_flip($this->States);
		return $States[$StateOrProvinceName];
	}
	
	/**
	 * Get the AVS (address verification) message.
	 *
	 * @access	public
	 * @param	string	$AVSCode	Address verification result code.
	 * @return	string	Returns the full message for the supplied AVS code.
	 */
	function GetAVSCodeMessage($AVSCode)
	{					  
		return $this->AVSCodes[$AVSCode];
	}
	
	/**
	 * Get the security digits (CVV2 Code) message.
	 *
	 * @access	public
	 * @param	string	$CVV2Code	Credit card security digits.
	 * @return	string	Returns the full message for the supplied CVV2 code.
	 */
	function GetCVV2CodeMessage($CVV2Code)
	{
		return $this->CVV2Codes[$CVV2Code];	
	}
	
	/**
	 * Get the currency code text value.
	 *
	 * @access	public
	 * @param	string	$CurrencyCode	Currency code that you would like the full text for.
	 * @return	string	Returns the full name for the supplied currency code.
	 */
	function GetCurrencyCodeText($CurrencyCode)
	{
		return $this->CurrencyCodes[$CurrencyCode];
	}
	
	/**
	 * Get the currency code based on the text value.
	 *
	 * @access	public
	 * @param	string	$CurrencyCodeText	Full name for a currency.
	 * @return	string	Returns the full name for the supplied currency code.
	 */
	function GetCurrencyCode($CurrencyCodeText)
	{
		$CurrencyCodes = array_flip($this->CurrencyCodes);
		return $CurrencyCodes[$CurrencyCodeText];
	}
	
	/**
	 * Send the API request to PayPal using CURL.
	 *
	 * @access	public
	 * @param	string	$Request		Raw API request string.
	 * @param	string	$APIName		The name of the API which you are calling.
	 * @param	string	$APIOperation	The method of the API which you are calling.
     * @param   string  $PrintHeaders   The option to print headers or not.
	 * @return	string	$Response		Returns the raw HTTP response from PayPal.
	 */
	function CURLRequest($Request = "", $APIName = "", $APIOperation = "", $PrintHeaders = false)
	{
		$curl = curl_init();
				// curl_setopt($curl, CURLOPT_HEADER,TRUE);
				curl_setopt($curl, CURLOPT_VERBOSE, $this->Sandbox);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($curl, CURLOPT_TIMEOUT, 30);
				curl_setopt($curl, CURLOPT_URL, $this->EndPointURL);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $Request);
				
		if($this->APIMode == 'Certificate')
		{
			curl_setopt($curl, CURLOPT_SSLCERT, $this->PathToCertKeyPEM);
		}
		
		$Response = curl_exec($curl);
		
		/*
		 * If a cURL error occurs, output it for review.
		 */
		if($this->Sandbox)
		{
			if(curl_error($curl))
			{
				echo curl_error($curl).'<br /><br />';	
			}
		}
				
		curl_close($curl);
		return $Response;	
	}
	
	/**
	 * Convert an NVP string to an array with URL decoded values.
	 *
	 * @access	public
	 * @param	string	$NVPString	Name-value-pair string that you would like to convert to an array.
	 * @return	mixed[]	Returns the NVP string as an array structure.
	 */
	function NVPToArray($NVPString)
	{
		$proArray = array();
		while(strlen($NVPString))
		{
			// name
			$keypos= strpos($NVPString,'=');
			$keyval = substr($NVPString,0,$keypos);
			// value
			$valuepos = strpos($NVPString,'&') ? strpos($NVPString,'&'): strlen($NVPString);
			$valval = substr($NVPString,$keypos+1,$valuepos-$keypos-1);
			// decoding the respose
			$proArray[$keyval] = urldecode($valval);
			$NVPString = substr($NVPString,$valuepos+1,strlen($NVPString));
		}
		
		return $proArray;
		
	}
	
	/**
	 * Check whether or not the API call was successful.
	 *
	 * @access	public
	 * @param	string	$ack	The value for ACK returned by a PayPal API response.
	 * @return	boolean	Returns a boolean (true/false) value for whether or not the ACK supplied is successful.
	 */
	function APICallSuccessful($ack)
	{
		if(strtoupper($ack) != 'SUCCESS' && strtoupper($ack) != 'SUCCESSWITHWARNING' && strtoupper($ack) != 'PARTIALSUCCESS')
		{
			return false;
		}
		else
		{
			return true;
		}
	}
		
	/**
	 * Get all errors returned from PayPal.
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	Array structure of PayPal NVP response.
	 * @return	mixed[]	$Errors		Returns an array structure of all errors / warnings returned in a PayPal HTTP response.
	 */
	function GetErrors($DataArray)
	{
	
		$Errors = array();
		$n = 0;
		while(isset($DataArray['L_ERRORCODE' . $n . '']))
		{
			$LErrorCode = isset($DataArray['L_ERRORCODE' . $n . '']) ? $DataArray['L_ERRORCODE' . $n . ''] : '';
			$LShortMessage = isset($DataArray['L_SHORTMESSAGE' . $n . '']) ? $DataArray['L_SHORTMESSAGE' . $n . ''] : '';
			$LLongMessage = isset($DataArray['L_LONGMESSAGE' . $n . '']) ? $DataArray['L_LONGMESSAGE' . $n . ''] : '';
			$LSeverityCode = isset($DataArray['L_SEVERITYCODE' . $n . '']) ? $DataArray['L_SEVERITYCODE' . $n . ''] : '';
			
			$CurrentItem = array(
								'L_ERRORCODE' => $LErrorCode, 
								'L_SHORTMESSAGE' => $LShortMessage, 
								'L_LONGMESSAGE' => $LLongMessage, 
								'L_SEVERITYCODE' => $LSeverityCode
								);
								
			array_push($Errors, $CurrentItem);
			$n++;
		}
		
		return $Errors;
		
	}
		
	
	/**
	 * Parse order items from an NVP string.
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	An array structure of a PayPal HTTP response.	
	 * @return	mixed[]	Returns an array structure of the order items included in a PayPal HTTP response.
	 */
	function GetOrderItems($DataArray)
	{
		
		$OrderItems = array();
		$n = 0;
		while(isset($DataArray['L_NAME' . $n . '']))
		{
			$LName = isset($DataArray['L_NAME' . $n . '']) ? $DataArray['L_NAME' . $n . ''] : '';
			$LDesc = isset($DataArray['L_DESC' . $n . '']) ? $DataArray['L_DESC' . $n . ''] : '';
			$LNumber = isset($DataArray['L_NUMBER' . $n . '']) ? $DataArray['L_NUMBER' . $n . ''] : '';
			$LQTY = isset($DataArray['L_QTY' . $n . '']) ? $DataArray['L_QTY' . $n . ''] : '';
			$LAmt = isset($DataArray['L_AMT' . $n . '']) ? $DataArray['L_AMT' . $n . ''] : '';
			$LTaxAmt = isset($DataArray['L_TAXAMT' . $n . '']) ? $DataArray['L_TAXAMT' . $n . ''] : '';
			$LOptionsName = isset($DataArray['L_OPTIONSNAME' . $n . '']) ? $DataArray['L_OPTIONSNAME' . $n . ''] : '';
			$LOptionsValue = isset($DataArray['L_OPTIONSVALUE' . $n . '']) ? $DataArray['L_OPTIONSVALUE' . $n . ''] : '';
			$LItemWeightValue = isset($DataArray['L_ITEMWEIGHTVALUE' . $n . '']) ? $DataArray['L_ITEMWEIGHTVALUE' . $n . ''] : '';
			$LItemWeightUnit = isset($DataArray['L_ITEMWEIGHTUNIT' . $n . '']) ? $DataArray['L_ITEMWEIGHTUNIT' . $n . ''] : '';
			$LItemWidthValue = isset($DataArray['L_ITEMWEIGHTVALUE' . $n . '']) ? $DataArray['L_ITEMWEIGHTVALUE' . $n . ''] : '';
			$LItemWidthUnit = isset($DataArray['L_ITEMWIDTHUNIT' . $n . '']) ? $DataArray['L_ITEMWIDTHUNIT' . $n . ''] : '';
			$LItemLengthValue = isset($DataArray['L_ITEMLENGTHVALUE' . $n . '']) ? $DataArray['L_ITEMLENGTHVALUE' . $n . ''] : '';
			$LItemLengthUnit = isset($DataArray['L_ITEMLENGTHUNIT' . $n . '']) ? $DataArray['L_ITEMLENGTHUNIT' . $n . ''] : '';
			$LeBayTransID = isset($DataArray['L_EBAYITEMTXNID' . $n . '']) ? $DataArray['L_EBAYITEMTXNID' . $n . ''] : '';
			$LeBayOrderID = isset($DataArray['L_EBAYITEMORDERID' . $n . '']) ? $DataArray['L_EBAYITEMORDERID' . $n . ''] : '';
			
			$CurrentItem = array(
								'L_NAME' => $LName, 
								'L_DESC' => $LDesc, 
								'L_NUMBER' => $LNumber, 
								'L_QTY' => $LQTY, 
								'L_AMT' => $LAmt, 
								'L_OPTIONSNAME' => $LOptionsName, 
								'L_OPTIONSVALUE' => $LOptionsValue, 
								'L_ITEMWEIGHTVALUE' => $LItemWeightValue, 
								'L_ITEMWEIGHTUNIT' => $LItemWeightUnit, 
								'L_ITEMWIDTHVALUE' => $LItemWidthValue, 
								'L_ITEMWIDTHUNIT' => $LItemWidthUnit, 
								'L_ITEMLENGTHVALUE' => $LItemLengthValue, 
								'L_ITEMLENGTHUNIT' => $LItemLengthUnit, 
								'L_TAXAMT' => $LTaxAmt, 
								'L_EBAYITEMTXNID' => $LeBayTransID, 
								'L_EBAYITEMORDERID' => $LeBayOrderID
								);
								
			array_push($OrderItems, $CurrentItem);
			$n++;
		}
		
		return $OrderItems;
	
	} // End function GetOrderItems

	
	/**
	 * Get all payment(s) details from an NVP string.
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	Array structure from a PayPal HTTP response.
	 * @return	mixed[]	Returns an array structure for all of the payments included in a PayPal NVP response.
	 */
	function GetPayments($DataArray)
	{
		$Payments = array();
		$n = 0;
		while(isset($DataArray['PAYMENTREQUEST_' . $n . '_AMT']))
		{			
			$Payment = array(
							'SHIPTONAME' => isset($DataArray['PAYMENTREQUEST_' . $n . '_SHIPTONAME']) ? $DataArray['PAYMENTREQUEST_' . $n . '_SHIPTONAME'] : '', 
							'SHIPTOSTREET' => isset($DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOSTREET']) ? $DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOSTREET'] : '', 
							'SHIPTOSTREET2' => isset($DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOSTREET2']) ? $DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOSTREET2'] : '', 
							'SHIPTOCITY' => isset($DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOCITY']) ? $DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOCITY'] : '', 
							'SHIPTOSTATE' => isset($DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOSTATE']) ? $DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOSTATE'] : '', 
							'SHIPTOZIP' => isset($DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOZIP']) ? $DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOZIP'] : '', 
							'SHIPTOCOUNTRYCODE' => isset($DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOCOUNTRYCODE']) ? $DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOCOUNTRYCODE'] : '', 
							'SHIPTOCOUNTRYNAME' => isset($DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOCOUNTRYNAME']) ? $DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOCOUNTRYNAME'] : '', 
							'SHIPTOPHONENUM' => isset($DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOPHONENUM']) ? $DataArray['PAYMENTREQUEST_' . $n . '_SHIPTOPHONENUM'] : '', 
							'ADDRESSSTATUS' => isset($DataArray['PAYMENTREQUEST_' . $n . '_ADDRESSSTATUS']) ? $DataArray['PAYMENTREQUEST_' . $n . '_ADDRESSSTATUS'] : '', 
							'AMT' => isset($DataArray['PAYMENTREQUEST_' . $n . '_AMT']) ? $DataArray['PAYMENTREQUEST_' . $n . '_AMT'] : '', 
							'CURRENCYCODE' => isset($DataArray['PAYMENTREQUEST_' . $n . '_CURRENCYCODE']) ? $DataArray['PAYMENTREQUEST_' . $n . '_CURRENCYCODE'] : '', 
							'ITEMAMT' => isset($DataArray['PAYMENTREQUEST_' . $n . '_ITEMAMT']) ? $DataArray['PAYMENTREQUEST_' . $n . '_ITEMAMT'] : '', 
							'SHIPPINGAMT' => isset($DataArray['PAYMENTREQUEST_' . $n . '_SHIPPINGAMT']) ? $DataArray['PAYMENTREQUEST_' . $n . '_SHIPPINGAMT'] : '', 
							'INSURANCEOPTIONOFFERED' => isset($DataArray['PAYMENTREQUEST_' . $n . '_INSURANCEOPTIONOFFERED']) ? $DataArray['PAYMENTREQUEST_' . $n . '_INSURANCEOPTIONOFFERED'] : '', 
							'HANDLINGAMT' => isset($DataArray['PAYMENTREQUEST_' . $n . '_HANDLINGAMT']) ? $DataArray['PAYMENTREQUEST_' . $n . '_HANDLINGAMT'] : '', 
							'TAXAMT' => isset($DataArray['PAYMENTREQUEST_' . $n . '_TAXAMT']) ? $DataArray['PAYMENTREQUEST_' . $n . '_TAXAMT'] : '', 
							'DESC' => isset($DataArray['PAYMENTREQUEST_' . $n . '_DESC']) ? $DataArray['PAYMENTREQUEST_' . $n . '_DESC'] : '', 
							'CUSTOM' => isset($DataArray['PAYMENTREQUEST_' . $n . '_CUSTOM']) ? $DataArray['PAYMENTREQUEST_' . $n . '_CUSTOM'] : '', 
							'INVNUM' => isset($DataArray['PAYMENTREQUEST_' . $n . '_INVNUM']) ? $DataArray['PAYMENTREQUEST_' . $n . '_INVNUM'] : '', 
							'NOTIFYURL' => isset($DataArray['PAYMENTREQUEST_' . $n . '_NOTIFYURL']) ? $DataArray['PAYMENTREQUEST_' . $n . '_NOTIFYURL'] : '', 
							'NOTETEXT' => isset($DataArray['PAYMENTREQUEST_' . $n . '_NOTETEXT']) ? $DataArray['PAYMENTREQUEST_' . $n . '_NOTETEXT'] : '', 
							'TRANSACTIONID' => isset($DataArray['PAYMENTREQUEST_' . $n . '_TRANSACTIONID']) ? $DataArray['PAYMENTREQUEST_' . $n . '_TRANSACTIONID'] : '', 
							'ALLOWEDPAYMENTMETHOD' => isset($DataArray['PAYMENTREQUEST_' . $n . '_ALLOWEDPAYMENTMETHOD']) ? $DataArray['PAYMENTREQUEST_' . $n . '_ALLOWEDPAYMENTMETHOD'] : '', 
							'PAYMENTREQUESTID' => isset($DataArray['PAYMENTREQUEST_' . $n . '_PAYMENTREQUESTID']) ? $DataArray['PAYMENTREQUEST_' . $n . '_PAYMENTREQUESTID'] : ''
							);
			
			$n_items = 0;
			$OrderItems = array();
			while(isset($DataArray['L_PAYMENTREQUEST_' . $n . '_AMT' . $n_items]))
			{
				$Item = array(
							'NAME' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_NAME' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_NAME' . $n_items] : '', 
							'DESC' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_DESC' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_DESC' . $n_items] : '', 
							'AMT' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_AMT' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_AMT' . $n_items] : '', 
							'NUMBER' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_NUMBER' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_NUMBER' . $n_items] : '', 
							'QTY' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_QTY' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_QTY' . $n_items] : '', 
							'TAXAMT' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_TAXAMT' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_TAXAMT' . $n_items] : '', 
							'ITEMWEIGHTVALUE' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMWEIGHTVALUE' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMWEIGHTVALUE' . $n_items] : '', 
							'ITEMWEIGHTUNIT' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMWEIGHTUNIT' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMWEIGHTUNIT' . $n_items] : '', 
							'ITEMLENGTHVALUE' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMLENGTHVALUE' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMLENGTHVALUE' . $n_items] : '', 
							'ITEMLENGTHUNIT' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMLENGTHUNIT' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMLENGTHUNIT' . $n_items] : '', 
							'ITEMWIDTHVALUE' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMWIDTHVALUE' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMWIDTHVALUE' . $n_items] : '', 
							'ITEMWIDTHUNIT' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMWIDTHUNIT' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMWIDTHUNIT' . $n_items] : '', 
							'ITEMHEIGHTVALUE' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMHEIGHTVALUE' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMHEIGHTVALUE' . $n_items] : '', 
							'ITEMHEIGHTUNIT' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMHEIGHTUNIT' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_ITEMHEIGHTUNIT' . $n_items] : '', 
							'EBAYITEMNUMBER' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_EBAYITEMNUMBER' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_EBAYITEMNUMBER' . $n_items] : '', 
							'EBAYAUCTIONTXNID' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_EBAYAUCTIONTXNID' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_EBAYAUCTIONTXNID' . $n_items] : '', 
							'EBAYITEMORDERID' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_EBAYITEMORDERID' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_EBAYITEMORDERID' . $n_items] : '', 
							'EBAYITEMCARTID' => isset($DataArray['L_PAYMENTREQUEST_' . $n . '_EBAYITEMCARTID' . $n_items]) ? $DataArray['L_PAYMENTREQUEST_' . $n . '_EBAYITEMCARTID' . $n_items] : ''
							);
				
				array_push($OrderItems, $Item);
				$n_items++;	
			}
			$Payment['ORDERITEMS'] = $OrderItems;
			
			array_push($Payments, $Payment);
			$n++;
		}
		
		return $Payments;
	}
			
	/**
	 * Mask the API credential values in the API call for logging purposes.
	 *
	 * @access	public
	 * @param	string	$api_result	Raw NVP string.
	 * @return	string	Returns the raw NVP string with the API credentials masked.
	 */
	function MaskAPIResult($api_result)
	{
		$api_result_array = $this->NVPToArray($api_result);
		$api_result='';
		if(isset($api_result_array['SIGNATURE']))
		{
			$api_result_array['USER'] = '*****';
			$api_result_array['PWD'] = '*****';
			$api_result_array['SIGNATURE'] = '*****';	
		}
                if(isset($api_result_array['ACCT']))
                {
                    $api_result_array['ACCT'] = '*****'.substr($api_result_array['ACCT'],-4);
                }
                if(isset($api_result_array['CVV2']))
                {
                    $api_result_array['CVV2'] = '*****';
                }
                $api_result=urldecode(http_build_query($api_result_array));
                return $api_result;
	}
	
    /**
     * Save log info to a location on the disk.
     *
     * @param $log_path
     * @param $filename
     * @param $string_data
     * @return bool
     */
    function Logger($log_path, $filename, $string_data)
	{

        if($this->LogResults)
        {
            $timestamp = strtotime('now');
            $timestamp = date('mdY_gi_s_A_',$timestamp);

            $string_data_array = $this->NVPToArray($string_data);

            $file = $log_path.$timestamp.$filename.'.txt';
            $fh = fopen($file, 'w');
            fwrite($fh, $string_data.chr(13).chr(13).print_r($string_data_array, true));
            fclose($fh);
        }
		
		return true;	
	}
	
	/**
	 * Issue a refund to the PayPal account holder associated with a transaction.
	 *
	 * This API operation can be used to issue a full or partial refund for any transaction 
	 * within a default period of 60 days from when the payment is received.
	 * 
	 * After the refund period has passed, merchants can no longer use the RefundTransaction 
	 * API operation to issue refunds. Instead, merchants can manually issue a credit to the 
	 * buyer by logging into their PayPal account; for PayPal payments, a credit can be issued 
	 * by clicking Send Money. Alternatively, merchants can use the MassPay API to credit 
	 * PayPal accounts or utilize the DoNonReferencedCredit API operation to issue a credit to 
	 * a card without referencing the original transaction.
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	Array structure of request data.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function RefundTransaction($DataArray)
	{
		$RTFieldsNVP = '&METHOD=RefundTransaction';
		
		$RTFields = isset($DataArray['RTFields']) ? $DataArray['RTFields'] : array();
        $MerchantDataVars = isset($DataArray['MerchantDataVars']) ? $DataArray['MerchantDataVars'] : array();
		
		foreach($RTFields as $RTFieldsVar => $RTFieldsVal)
		{
			$RTFieldsNVP .= $RTFieldsVal != '' ? '&' . strtoupper($RTFieldsVar) . '=' . urlencode($RTFieldsVal) : '';
		}

        $n = 0;
        foreach($MerchantDataVars as $MerchantData)
        {
            $MerchantDataKey = $MerchantData['key'];
            $MerchantDataValue = $MerchantData['value'];
            $RTFieldsNVP .= $MerchantData['value'] != '' ? '&MERCHANTDATA' . $n . 'KEY=' . urlencode($MerchantData['key']) .
                '&MERCHANTDATA' . $n . 'VALUE=' . urlencode($MerchantData['value']) : '';
            $n++;
        }
		
		$NVPRequest = $this->NVPCredentials . $RTFieldsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
									
		return $NVPResponseArray;
	
	}
	
	/**
	 * Obtain information about a specific transaction.
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	Array structure of request data.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function GetTransactionDetails($DataArray)
	{		
		$GTDFieldsNVP = '&METHOD=GetTransactionDetails';
		
		$GTDFields = isset($DataArray['GTDFields']) ? $DataArray['GTDFields'] : array();
		
		foreach($GTDFields as $GTDFieldsVar => $GTDFieldsVal)
		{
			$GTDFieldsNVP .= $GTDFieldsVal != '' ? '&' . strtoupper($GTDFieldsVar) . '=' . urlencode($GTDFieldsVal) : '';
		}
		
		$NVPRequest = $this->NVPCredentials . $GTDFieldsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);
		$OrderItems = $this->GetOrderItems($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['ORDERITEMS'] = $OrderItems;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $this->MaskAPIResult($NVPRequest);
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
								
		return $NVPResponseArray;
	
	}
	
	
	/**
	 * Initiate an Express Checkout transaction.
	 * 
	 * Used to generate a unique TOKEN for use with the checkout.
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	Array structure of request data.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function SetExpressCheckout($DataArray)
	{	
		$SECFieldsNVP = '&METHOD=SetExpressCheckout';
		$SurveyChoicesNVP = '';
		$PaymentsNVP = '';
		$ShippingOptionsNVP = '';
		$BillingAgreementsNVP = '';
		
		// SetExpressCheckout Request Fields
		$SECFields = isset($DataArray['SECFields']) ? $DataArray['SECFields'] : array();
		foreach($SECFields as $SECFieldsVar => $SECFieldsVal)
		{
			if(strtoupper($SECFieldsVar) != 'SKIPDETAILS')
			{
				$SECFieldsNVP .= $SECFieldsVal != '' ? '&' . strtoupper($SECFieldsVar) . '=' . urlencode($SECFieldsVal) : '';
			}
			else
			{
				$SkipDetails = $SECFieldsVal ? true : false;				
			}
		}
		
		// Check to see if the REDIRECTURL should include user-action
		if(isset($SkipDetails) && $SkipDetails)
		{
			$SkipDetailsOption = 'useraction=commit';
		}
		else
		{
			$SkipDetailsOption = 'useraction=continue';
		}
		
		// Survey Choices
		$SurveyChoices = isset($DataArray['SurveyChoices']) ? $DataArray['SurveyChoices'] : array();
		if(isset($SECFields['surveyquestion']) && $SECFields['surveyquestion'] != '')
		{
			$n = 0;
			foreach($SurveyChoices as $SurveyChoice)
			{
				$SurveyChoicesNVP .= $SurveyChoice != '' ? '&' . 'L_SURVEYCHOICE' . $n . '=' . urlencode($SurveyChoice) : '';
				$n++;	
			}
		}
		
		// Payment Details Type Fields
		$Payments = isset($DataArray['Payments']) ? $DataArray['Payments'] : array();
		$n = 0;
		foreach($Payments as $PaymentsVar => $PaymentsVal)
		{
			$CurrentPayment = $Payments[$PaymentsVar];
			foreach($CurrentPayment as $CurrentPaymentVar => $CurrentPaymentVal)
			{
				if(strtoupper($CurrentPaymentVar) != 'ORDER_ITEMS')
				{
					$PaymentsNVP .= $CurrentPaymentVal != '' ? '&PAYMENTREQUEST_' . $n . '_' . strtoupper($CurrentPaymentVar) . '=' . urlencode($CurrentPaymentVal) : '';
				}
				else
				{
					$PaymentOrderItems = $CurrentPayment['order_items'];
					$n_item = 0;
					foreach($PaymentOrderItems as $OrderItemsVar => $OrderItemsVal)
					{
						$CurrentItem = $PaymentOrderItems[$OrderItemsVar];
						foreach($CurrentItem as $CurrentItemVar => $CurrentItemVal)
						{	
							$PaymentsNVP .= $CurrentItemVal != '' ? '&L_PAYMENTREQUEST_' . $n . '_' . strtoupper($CurrentItemVar) . $n_item . '=' . urlencode($CurrentItemVal) : '';
						}
						$n_item++;
					}	
				}
			}
			$n++;
		}
		
		// Billing Agreements
		$BillingAgreements = isset($DataArray['BillingAgreements']) ? $DataArray['BillingAgreements'] : array();
		$n = 0;
		foreach($BillingAgreements as $BillingAgreementVar => $BillingAgreementVal)
		{
			$CurrentItem = $BillingAgreements[$BillingAgreementVar];
			foreach($CurrentItem as $CurrentItemVar => $CurrentItemVal)
			{
				$BillingAgreementsNVP .= $CurrentItemVal != '' ? '&' . strtoupper($CurrentItemVar) . $n . '=' . urlencode($CurrentItemVal) : '';
			}
			$n++;	
		}
			
		// Shipping Options Fields
		$ShippingOptions = isset($DataArray['ShippingOptions']) ? $DataArray['ShippingOptions'] : array();
		$n = 0;
		foreach($ShippingOptions as $ShippingOptionsVar => $ShippingOptionsVal)
		{
			$CurrentOption = $ShippingOptions[$ShippingOptionsVar];
			foreach($CurrentOption as $CurrentOptionVar => $CurrentOptionVal)
			{
				$ShippingOptionsNVP .= $CurrentOptionVal != '' ? '&' . strtoupper($CurrentOptionVar) . $n . '=' . urlencode($CurrentOptionVal) : '';
			}
			$n++;	
		}
		
		$NVPRequest = $this->NVPCredentials . $SECFieldsNVP . $SurveyChoicesNVP . $ShippingOptionsNVP . $BillingAgreementsNVP . $PaymentsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		if(isset($NVPResponseArray['TOKEN']) && $NVPResponseArray['TOKEN'] != '')
		{
			if($this->Sandbox)
			{
				$NVPResponseArray['REDIRECTURLDIGITALGOODS'] = 'https://www.sandbox.paypal.com/incontext?'.$SkipDetailsOption.'&token='.$NVPResponseArray['TOKEN'];
				$NVPResponseArray['REDIRECTURL'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&' . $SkipDetailsOption . '&token=' . $NVPResponseArray['TOKEN'];	
			}
			else
			{
				$NVPResponseArray['REDIRECTURLDIGITALGOODS'] = 'https://www.paypal.com/incontext?'.$SkipDetailsOption.'&token='.$NVPResponseArray['TOKEN'];
				$NVPResponseArray['REDIRECTURL'] = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&' . $SkipDetailsOption . '&token=' . $NVPResponseArray['TOKEN'];
			}
		}
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
				
		return $NVPResponseArray;
	
	}  // End function SetExpressCheckout()		
	
	/**
	 * Obtain details about an Express Checkout transaction.
	 *
	 * This is used after PayPal redirects the buyer back to your 
	 * ReturnURL supplied in the SetExpressCheckout request.  Data 
	 * returned includes the buyer's name, shipping address, phone number, 
	 * and general transaction details.
	 *
	 * @access	public
	 * @param	string	$Token	The token returned from a previous SetExpressCheckout request.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function GetExpressCheckoutDetails($Token)
	{
		$GECDFieldsNVP = '&METHOD=GetExpressCheckoutDetails&TOKEN=' . $Token;
			
		$NVPRequest = $this->NVPCredentials . $GECDFieldsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);
		$OrderItems = $this->GetOrderItems($NVPResponseArray);
		$Payments = $this->GetPayments($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['ORDERITEMS'] = $OrderItems;
		$NVPResponseArray['PAYMENTS'] = $Payments;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
				
		return $NVPResponseArray;
		
	
	}  // End function GetExpressCheckoutDetails()
	
	/**
	 * Complete an Express Checkout transaction.
	 *
	 * If you set up a billing agreement in your SetExpressCheckout API call, 
	 * the billing agreement is created when you call the DoExpressCheckoutPayment 
	 * API operation.
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	Array structure of request data.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function DoExpressCheckoutPayment($DataArray)
	{
		$DECPFieldsNVP = '&METHOD=DoExpressCheckoutPayment';
		$PaymentsNVP = '';
		$UserSelectedOptionsNVP = '';
		
		// DoExpressCheckoutPayment Fields
		$DECPFields = isset($DataArray['DECPFields']) ? $DataArray['DECPFields'] : array();
		foreach($DECPFields as $DECPFieldsVar => $DECPFieldsVal)
		{
			$DECPFieldsNVP .= $DECPFieldsVal != '' ? '&' . strtoupper($DECPFieldsVar) . '=' . urlencode($DECPFieldsVal) : '';
		}
		
		// Payment Details Type Fields
		$Payments = isset($DataArray['Payments']) ? $DataArray['Payments'] : array();
		$n = 0;
		foreach($Payments as $PaymentsVar => $PaymentsVal)
		{
			$CurrentPayment = $Payments[$PaymentsVar];
			foreach($CurrentPayment as $CurrentPaymentVar => $CurrentPaymentVal)
			{
				if(strtoupper($CurrentPaymentVar) == 'ORDER_ITEMS')
				{
                    $PaymentOrderItems = $CurrentPayment['order_items'];
                    $n_item = 0;
                    foreach($PaymentOrderItems as $OrderItemsVar => $OrderItemsVal)
                    {
                        $CurrentItem = $PaymentOrderItems[$OrderItemsVar];
                        foreach ($CurrentItem as $CurrentItemVar => $CurrentItemVal)
                        {
                            $PaymentsNVP .= $CurrentItemVal != '' ? '&L_PAYMENTREQUEST_' . $n . '_' . strtoupper($CurrentItemVar) . $n_item . '=' . urlencode($CurrentItemVal) : '';
                        }
                        $n_item++;
                    }
				}
                elseif(strtoupper($CurrentPaymentVar) == 'REDEEMED_OFFERS')
                {
                    $RedeemedOffers = $CurrentPayment['redeemed_offers'];
                    $n_item = 0;
                    foreach($RedeemedOffers as $RedeemedOfferVar => $RedeemedOfferVal)
                    {
                        $CurrentRedeemedOffer = $RedeemedOffers[$RedeemedOfferVar];
                        foreach ($CurrentRedeemedOffer as $CurrentRedeemedOfferVar => $CurrentRedeemedOfferVal)
                        {
                            $PaymentsNVP .= $CurrentRedeemedOfferVal != '' ? '&L_PAYMENTREQUEST_' . $n . '_' . strtoupper($CurrentRedeemedOfferVar) . $n_item . '=' . urlencode($CurrentRedeemedOfferVal) : '';
                        }
                        $n_item++;
                    } 
                }
                elseif(strtoupper($CurrentPaymentVar) == 'MERCHANT_DATA_VARS')
                {
                    $MerchantDataVars = $CurrentPayment['merchant_data_vars'];
                    $n_item = 0;
                    foreach($MerchantDataVars as $MerchantDataVarsVar => $MerchantDataVarsVal)
                    {
                        $CurrentMerchantDataVar = $MerchantDataVars[$MerchantDataVarsVar];
                        foreach ($CurrentMerchantDataVar as $CurrentMerchantDataVarVar => $CurrentMerchantDataVarVal)
                        {
                            $PaymentsNVP .= $CurrentMerchantDataVarVal != '' ? '&L_PAYMENTREQUEST_' . $n . '_' . strtoupper($CurrentMerchantDataVarVar) . $n_item . '=' . urlencode($CurrentMerchantDataVarVal) : '';
                        }
                        $n_item++;
                    } 
                }
				else
				{
                    $PaymentsNVP .= $CurrentPaymentVal != '' ? '&PAYMENTREQUEST_' . $n . '_' . strtoupper($CurrentPaymentVar) . '=' . urlencode($CurrentPaymentVal) : '';
				}
			}
			$n++;
		}	
		
		// User Selected Options
		$UserSelectedOptions = isset($DataArray['UserSelectedOptions']) ? $DataArray['UserSelectedOptions'] : array();
		foreach($UserSelectedOptions as $UserSelectedOptionVar => $UserSelectedOptionVal)
		{
			$UserSelectedOptionsNVP .= $UserSelectedOptionVal != '' ? '&' . strtoupper($UserSelectedOptionVar) . '=' . urlencode($UserSelectedOptionVal) : '';
		}
		
		$NVPRequest = $this->NVPCredentials . $DECPFieldsNVP . $PaymentsNVP . $UserSelectedOptionsNVP;
                $this->MaskAPIResult($NVPRequest);
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		// Loop through all possible payments and parse out data accordingly.
		// This is to handle parallel payments.
		$n = 0;
		$Payments = array();
		while(isset($NVPResponseArray['PAYMENTINFO_' . $n . '_AMT']))
		{
			$Payment = array(
								'TRANSACTIONID' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_TRANSACTIONID']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_TRANSACTIONID'] : '', 
								'TRANSACTIONTYPE' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_TRANSACTIONTYPE']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_TRANSACTIONTYPE'] : '', 
								'PAYMENTTYPE' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_PAYMENTTYPE']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_PAYMENTTYPE'] : '', 
								'ORDERTIME' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_ORDERTIME']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_ORDERTIME'] : '', 
								'AMT' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_AMT']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_AMT'] : '', 
								'FEEAMT' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_FEEAMT']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_FEEAMT'] : '', 
								'SETTLEAMT' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_SETTLEAMT']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_SETTLEAMT'] : '', 
								'TAXAMT' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_TAXAMT']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_TAXAMT'] : '', 
								'EXCHANGERATE' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_EXCHANGERATE']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_EXCHANGERATE'] : '', 
								'CURRENCYCODE' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_CURRENCYCODE']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_CURRENCYCODE'] : '', 
								'PAYMENTSTATUS' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_PAYMENTSTATUS']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_PAYMENTSTATUS'] : '', 
								'PENDINGREASON' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_PENDINGREASON']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_PENDINGREASON'] : '', 
								'REASONCODE' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_REASONCODE']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_REASONCODE'] : '', 
								'PROTECTIONELIGIBILITY' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_PROTECTIONELIGIBILITY']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_PROTECTIONELIGIBILITY'] : '', 
								'ERRORCODE' => isset($NVPResponseArray['PAYMENTINFO_' . $n . '_ERRORCODE']) ? $NVPResponseArray['PAYMENTINFO_' . $n . '_ERRORCODE'] : ''
								);
			
			// Pull out FMF info for current payment.													
			$FMFilters = array();
			$n_filters = 0;
			while(isset($NVPResponseArray['L_PAYMENTINFO_' . $n . '_FMFFILTERID' . $n_filters]))
			{
				$FMFilter = array(
								'ID' => isset($NVPResponseArray['L_PAYMENTINFO_' . $n . '_FMFFILTERID' . $n_filters]) ? $NVPResponseArray['L_PAYMENTINFO_' . $n . '_FMFFILTERID' . $n_filters] : '', 
								'NAME' => isset($NVPResponseArray['L_PAYMENTINFO_' . $n . '_FMFFILTERNAME' . $n_filters]) ? $NVPResponseArray['L_PAYMENTINFO_' . $n . '_FMFFILTERNAME' . $n_filters] : ''
								);
				$n_filters++;
			}
			$Payment['FMFILTERS'] = $FMFilters;
			
			// Pull error info for current payment.
			$PaymentErrors = array();
			while(isset($NVPResponseArray['PAYMENTREQUEST_' . $n . '_ERRORCODE']))
			{
				$Error = array(
								'ERRORCODE' => isset($NVPResponseArray['PAYMENTREQUEST_' . $n . '_ERRORCODE']) ? $NVPResponseArray['PAYMENTREQUEST_' . $n . '_ERRORCODE'] : '', 
								'SHORTMESSAGE' => isset($NVPResponseArray['PAYMENTREQUEST_' . $n . '_SHORTMESSAGE']) ? $NVPResponseArray['PAYMENTREQUEST_' . $n . '_SHORTMESSAGE'] : '', 
								'LONGMESSAGE' => isset($NVPResponseArray['PAYMENTREQUEST_' . $n . '_LONGMESSAGE']) ? $NVPResponseArray['PAYMENTREQUEST_' . $n . '_LONGMESSAGE'] : '', 
								'SEVERITYCODE' => isset($NVPResponseArray['PAYMENTREQUEST_' . $n . '_SEVERITYCODE']) ? $NVPResponseArray['PAYMENTREQUEST_' . $n . '_SEVERITYCODE'] : '', 
								'ACK' => isset($NVPResponseArray['PAYMENTREQUEST_' . $n . '_ACK']) ? $NVPResponseArray['PAYMENTREQUEST_' . $n . '_ACK'] : ''								
								);
				array_push($PaymentErrors, $Error);
			}
			$Payment['ERRORS'] = $PaymentErrors;
			
			array_push($Payments, $Payment);
			$n++;	
		}
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['PAYMENTS'] = $Payments;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
		
		return $NVPResponseArray;
	
	}

	/**
	 * Search PayPal transaction history for transactions that meet the specified criteria.
	 *
	 * The maximum number of transactions that can be returned from a TransactionSearch API call is 100.
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	Array structure of request data.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function TransactionSearch($DataArray)
	{
		$TSFieldsNVP = '&METHOD=TransactionSearch';
		$PayerNameNVP = '';
		
		// Transaction Search Fields
		$TSFields = isset($DataArray['TSFields']) ? $DataArray['TSFields'] : array();
		foreach($TSFields as $TSFieldsVar => $TSFieldsVal)
		{
			$TSFieldsNVP .= $TSFieldsVal != '' ? '&' . strtoupper($TSFieldsVar) . '=' . urlencode($TSFieldsVal) : '';
		}
		
		// Payer Name Fields
		$PayerName = isset($DataArray['PayerName']) ? $DataArray['PayerName'] : array();
		foreach($PayerName as $PayerNameVar => $PayerNameVal)
		{
			$PayerNameNVP .= $PayerNameVal != '' ? '&' . strtoupper($PayerNameVar) . '=' . urlencode($PayerNameVal) : '';
		}
		
		$NVPRequest = $this->NVPCredentials . $TSFieldsNVP . $PayerNameNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$SearchResults = array();
		$n = 0;
		while(isset($NVPResponseArray['L_TIMESTAMP' . $n . '']))
		{
			$LTimestamp = isset($NVPResponseArray['L_TIMESTAMP' . $n . '']) ? $NVPResponseArray['L_TIMESTAMP' . $n . ''] : '';
			$LTimeZone = isset($NVPResponseArray['L_TIMEZONE' . $n . '']) ? $NVPResponseArray['L_TIMEZONE' . $n . ''] : '';
			$LType = isset($NVPResponseArray['L_TYPE' . $n . '']) ? $NVPResponseArray['L_TYPE' . $n . ''] : '';
			$LEmail = isset($NVPResponseArray['L_EMAIL' . $n . '']) ? $NVPResponseArray['L_EMAIL' . $n . ''] : '';
			$LName = isset($NVPResponseArray['L_NAME' . $n . '']) ? $NVPResponseArray['L_NAME' . $n . ''] : '';
			$LTransID = isset($NVPResponseArray['L_TRANSACTIONID' . $n . '']) ? $NVPResponseArray['L_TRANSACTIONID' . $n . ''] : '';
			$LStatus = isset($NVPResponseArray['L_STATUS' . $n . '']) ? $NVPResponseArray['L_STATUS' . $n . ''] : '';
			$LAmt = isset($NVPResponseArray['L_AMT' . $n . '']) ? $NVPResponseArray['L_AMT' . $n . ''] : '';
			$LFeeAmt = isset($NVPResponseArray['L_FEEAMT' . $n . '']) ? $NVPResponseArray['L_FEEAMT' . $n . ''] : '';
			$LNetAmt = isset($NVPResponseArray['L_NETAMT' . $n . '']) ? $NVPResponseArray['L_NETAMT' . $n . ''] : '';
			
			$CurrentItem = array(
								'L_TIMESTAMP' => $LTimestamp, 
								'L_TIMEZONE' => $LTimeZone, 
								'L_TYPE' => $LType, 
								'L_EMAIL' => $LEmail, 
								'L_NAME' => $LName, 
								'L_TRANSACTIONID' => $LTransID, 
								'L_STATUS' => $LStatus, 
								'L_AMT' => $LAmt, 
								'L_FEEAMT' => $LFeeAmt, 
								'L_NETAMT' => $LNetAmt
								);
																	
			array_push($SearchResults, $CurrentItem);
			$n++;
		}
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['SEARCHRESULTS'] = $SearchResults;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
		
		return $NVPResponseArray;
		
	
	}
	
	
	/**
	 * Process a payment from a buyer's account, which is identified by a previous transaction.
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	Array structure of request data.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function DoReferenceTransaction($DataArray)
	{	
		$DRTFieldsNVP = '&METHOD=DoReferenceTransaction';
		$CCDetailsNVP = '';
		$PayerInfoNVP = '';
		$BillingAddressNVP = '';
		$ShippingAddressNVP = '';
		$PaymentDetailsNVP = '';
		$OrderItemsNVP = '';
		
		// DoReferenceTransaction Fields
		$DRTFields = isset($DataArray['DRTFields']) ? $DataArray['DRTFields'] : array();
		foreach($DRTFields as $DRTFieldsVar => $DRTFieldsVal)
		{
			$DRTFieldsNVP .= $DRTFieldsVal != '' ? '&' . strtoupper($DRTFieldsVar) . '=' . urlencode($DRTFieldsVal) : '';
		}
		
		// Ship To Address Fields
		$ShippingAddress = isset($DataArray['ShippingAddress']) ? $DataArray['ShippingAddress'] : array();
		foreach($ShippingAddress as $ShippingAddressVar => $ShippingAddressVal)
		{
			$ShippingAddressNVP .= $ShippingAddressVal != '' ? '&' . strtoupper($ShippingAddressVar) . '=' . urlencode($ShippingAddressVal) : '';
		}
		
		// Payment Details Item Type Fields
		$OrderItems = isset($DataArray['OrderItems']) ? $DataArray['OrderItems'] : array();
		$n = 0;
		foreach($OrderItems as $OrderItemsVar => $OrderItemsVal)
		{
			$CurrentItem = $OrderItems[$OrderItemsVar];
			foreach($CurrentItem as $CurrentItemVar => $CurrentItemVal)
			{
				$OrderItemsNVP .= $CurrentItemVal != '' ? '&' . strtoupper($CurrentItemVar) . $n . '=' . urlencode($CurrentItemVal) : '';
			}
			$n++;
		}
			
		// CC Details Fields
		$CCDetails = isset($DataArray['CCDetails']) ? $DataArray['CCDetails'] : array();
		foreach($CCDetails as $CCDetailsVar => $CCDetailsVal)
		{
			$CCDetailsNVP .= $CCDetailsVal != '' ? '&' . strtoupper($CCDetailsVar) . '=' . urlencode($CCDetailsVal) : '';
		}
		
		// PayerInfo Type Fields
		$PayerInfo = isset($DataArray['PayerInfo']) ? $DataArray['PayerInfo'] : array();
		foreach($PayerInfo as $PayerInfoVar => $PayerInfoVal)
		{
			$PayerInfoNVP .= $PayerInfoVal != '' ? '&' . strtoupper($PayerInfoVar) . '=' . urlencode($PayerInfoVal) : '';
		}
		
		// Address Fields (Billing)
		$BillingAddress = isset($DataArray['BillingAddress']) ? $DataArray['BillingAddress'] : array();
		foreach($BillingAddress as $BillingAddressVar => $BillingAddressVal)
		{
			$BillingAddressNVP .= $BillingAddressVal != '' ? '&' . strtoupper($BillingAddressVar) . '=' . urlencode($BillingAddressVal) : '';
		}
		
		// Payment Details Fields
		$PaymentDetails = isset($DataArray['PaymentDetails']) ? $DataArray['PaymentDetails'] : array();
		foreach($PaymentDetails as $PaymentDetailsVar => $PaymentDetailsVal)
		{
			$PaymentDetailsNVP .= $PaymentDetailsVal != '' ? '&' . strtoupper($PaymentDetailsVar) . '=' . urlencode($PaymentDetailsVal) : '';
		}
		
		$NVPRequest = $this->NVPCredentials . $DRTFieldsNVP . $ShippingAddressNVP . $OrderItemsNVP . $CCDetailsNVP . $PayerInfoNVP . $BillingAddressNVP . $PaymentDetailsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['REQUESTDATA'] = $this->NVPToArray($this->MaskAPIResult($NVPRequest));
		$NVPResponseArray['RAWREQUEST'] = $this->MaskAPIResult($NVPRequest);
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
		
		return $NVPResponseArray;
	}		
		
	
	/**
	 * Create a billing agreement with a PayPal account holder.
	 *
	 * CreateBillingAgreement is only valid for reference transactions.
	 *
	 * @access public
	 * @param string $Token	A token returned from a previous SetExpressCheckout request.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 *
	 */
	function CreateBillingAgreement($Token)
	{
		$CBAFieldsNVP = '&METHOD=CreateBillingAgreement&TOKEN='.urlencode($Token);
		
		$NVPRequest = $this->NVPCredentials . $CBAFieldsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
								
		return $NVPResponseArray;	
	}
	
	/**
	 * Initiate the creation of a billing agreement.
	 * 
	 * When using Express Checkout with version 54.0 or later of the API, 
	 * do not use SetCustomerBillingAgreement and GetBillingAgreementCustomerDetails.  
	 * Instead, use SetExpressCheckout and set the amount value to zero.  Call
	 * GetExpressCheckoutDetails to obtain information about the buyer's checkout 
	 * status.  Then use the CreateBillingAgreement API to create the abilling agreement.
	 *
	 * @access	public
	 * @deprecated No longer used in PayPal API version 54.0 or higher.
	 * @param	mixed[]	$DataArray	Array structure of request data.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function SetCustomerBillingAgreement($DataArray)
	{	
		$SCBAFieldsNVP = '&METHOD=SetCustomerBillingAgreement';
		$BillingAgreementsNVP = '';
		
		$SCBAFields = isset($DataArray['SCBAFields']) ? $DataArray['SCBAFields'] : array();
		foreach($SCBAFields as $SCBAFieldsVar => $SCBAFieldsVal)
		{
			$SCBAFieldsNVP .= $SCBAFieldsVal != '' ? '&' . strtoupper($SCBAFieldsVar) . '=' . urlencode($SCBAFieldsVal) : '';
		}
		
		$BillingAgreements = isset($DataArray['BillingAgreements']) ? $DataArray['BillingAgreements'] : array();
		$n = 0;
		foreach($BillingAgreements as $BillingAgreementVar => $BillingAgreementVal)
		{
			$CurrentItem = $BillingAgreements[$BillingAgreementVar];
			foreach($CurrentItem as $CurrentItemVar => $CurrentItemVal)
			{
				$BillingAgreementsNVP .= $CurrentItemVal != '' ? '&' . strtoupper($CurrentItemVar) . $n . '=' . urlencode($CurrentItemVal) : '';
			}
			$n++;	
		}
		
		$NVPRequest = $this->NVPCredentials . $SCBAFieldsNVP . $BillingAgreementsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
								
		return $NVPResponseArray;
	}
	
	/**
	 * Obtain information about a billing agreement's PayPal account holder
	 *
	 * When using Express Checkout with version 54.0 or later of the API, 
	 * do not use SetCustomerBillingAgreement and GetBillingAgreementCustomerDetails.  
	 * Instead, use SetExpressCheckout and set the amount value to zero.  Call
	 * GetExpressCheckoutDetails to obtain information about the buyer's checkout 
	 * status.  Then use the CreateBillingAgreement API to create the abilling agreement.

	 * @access	public
	 * @deprecated No longer used in PayPal API version 54.0 or higher.
	 * @param	string $Token	Token returned by a previous SetCustomerBillingAgreement request.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function GetBillingAgreementCustomerDetails($Token)
	{
		$GBACDFieldsNVP = '&METHOD=GetBillingAgreementCustomerDetails&TOKEN=' . $Token;
			
		$NVPRequest = $this->NVPCredentials . $GBACDFieldsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
				
		return $NVPResponseArray;
	}
	
	/**
	 * Update or delete a billing agreement. 
	 *
	 * @access	public
	 * @param	mixed[]	$DataArray	Array structure of request data.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function BillAgreementUpdate($DataArray)
	{
		$BAUFieldsNVP = '&METHOD=BillAgreementUpdate';
		
		$BAUFields = isset($DataArray['BAUFields']) ? $DataArray['BAUFields'] : array();
		foreach($BAUFields as $BAUFieldsVar => $BAUFieldsVal)
		{
			$BAUFieldsNVP .= $BAUFieldsVal != '' ? '&' . strtoupper($BAUFieldsVar) . '=' . urlencode($BAUFieldsVal) : '';
		}
		
		$NVPRequest = $this->NVPCredentials . $BAUFieldsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
								
		return $NVPResponseArray;
			
	}
	
	/**
	 * Retrieve the current API permissions granted for the application.
	 *
	 * @access	public
	 * @deprecated	No longer used by PayPal.
	 * @param	string	$Token
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function GetAccessPermissionsDetails($Token)
	{
		$GetAccessPermissionsDetailsNVP = '&METHOD=GetAccessPermissionsDetails&TOKEN=' . $Token;
		
		$NVPRequest = $this->NVPCredentials . $GetAccessPermissionsDetailsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$Permissions = array();
		$n = 0;
		while(isset($NVPResponseArray['L_ACCESSPERMISSIONNAME' . $n . '']))
		{
			$LName = isset($NVPResponseArray['L_ACCESSPERMISSIONNAME' . $n . '']) ? $NVPResponseArray['L_ACCESSPERMISSIONNAME' . $n . ''] : '';
			$LStatus = isset($NVPResponseArray['L_ACCESSPERMISSIONSTATUS' . $n . '']) ? $NVPResponseArray['L_ACCESSPERMISSIONSTATUS' . $n . ''] : '';
			
			$CurrentItem = array(
								'L_ACCESSPERMISSIONNAME' => $LName, 
								'L_ACCESSPERMISSIONSTATUS' => $LStatus
								);
																	
			array_push($ActivePermissions, $CurrentItem);
			$n++;	
		}
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['Permissions'] = $Permissions;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
								
		return $NVPResponseArray;	
	}
	
	/**
	 * Set the access permissions for an application on a 3rd party user's account.
	 *
	 * @access	public
	 * @deprecated	No longer used by PayPal.
	 * @param	mixed[]	$DataArray	Array structure of request data.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function SetAccessPermissions($DataArray)
	{
		$SetAccessPermissionsNVP = '&METHOD=SetAccessPermissions';
		
		// SetAccessPermissions Fields
		$SetAccessPermissionsFields = isset($DataArray['SetAccessPermissionsFields']) ? $DataArray['SetAccessPermissionsFields'] : array();
		foreach($SetAccessPermissionsFields as $SetAccessPermissionsFieldsVar => $SetAccessPermissionsFieldsVal)
		{
			$SetAccessPermissionsNVP .= $SetAccessPermissionsFieldsVal != '' ? '&' . strtoupper($SetAccessPermissionsFieldsVar) . '=' . urlencode($SetAccessPermissionsFieldsVal) : '';
		}
		
		$n = 0;
		$RequiredPermissions = isset($DataArray['RequiredPermissions']) ? $DataArray['RequiredPermissions'] : array();
		foreach($RequiredPermissions as $RequiredPermission)
		{
			$SetAccessPermissionsNVP .= '&L_REQUIREDACCESSPERMISSIONS' . $n . '=' . urlencode($RequiredPermission);
			$n++;
		}
		
		$n = 0;
		$OptionalPermissions = isset($DataArray['OptionalPermissions']) ? $DataArray['OptionalPermissions'] : array();
		foreach($OptionalPermissions as $OptionalPermission)
		{
			$SetAccessPermissionsNVP .= '&L_OPTIONALACCESSPERMISSIONS' . $n . '=' . urlencode($OptionalPermission);
			$n++;
		}
		
		$NVPRequest = $this->NVPCredentials . $SetAccessPermissionsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);
		$Token = isset($NVPResponseArray['TOKEN']) ? $NVPResponseArray['TOKEN'] : '';

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		if($this->Sandbox)
		{
			$RedirectURL = $Token != '' ? 'https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_access-permission-login&token=' . $Token : '';
			$LogoutRedirectURL = $Token != '' ? 'https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_access-permission-logout&token=' . $Token : '';
		}
		else
		{
			$RedirectURL = $Token != '' ? 'https://www.paypal.com/us/cgi-bin/webscr?cmd=_access-permission-login&token=' . $Token : '';
			$LogoutRedirectURL = $Token != '' ? 'https://www.paypal.com/us/cgi-bin/webscr?cmd=_access-permission-logout&token=' . $Token : '';
		}
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['REDIRECTURL'] = $RedirectURL;
		$NVPResponseArray['LOGOUTREDIRECTURL'] = $LogoutRedirectURL;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
								
		return $NVPResponseArray;
	}
	
	/**
	 * Update the access permissions for an application on a 3rd party user's account.
	 *
	 * @access	public
	 * @deprecated	No longer used by PayPal.
	 * @param	string $PayerID	Payer ID of the PayPal user.
	 * @return	mixed[]	Returns an array structure of the PayPal HTTP response params as well as parsed errors and the raw request/response.
	 */
	function UpdateAccessPermissions($PayerID)
	{
		$UpdateAcccessPermissionsNVP = '&METHOD=UpdateAccessPermissions&PAYERID=' . $PayerID;	
		
		$NVPRequest = $this->NVPCredentials . $UpdateAcccessPermissionsNVP;
		$NVPResponse = $this->CURLRequest($NVPRequest);
		$NVPRequestArray = $this->NVPToArray($NVPRequest);
		$NVPResponseArray = $this->NVPToArray($NVPResponse);
		
		$Errors = $this->GetErrors($NVPResponseArray);

        $this->Logger($this->LogPath, __FUNCTION__.'Request', $this->MaskAPIResult($NVPRequest));
        $this->Logger($this->LogPath, __FUNCTION__.'Response', $NVPResponse);
		
		$NVPResponseArray['ERRORS'] = $Errors;
		$NVPResponseArray['REQUESTDATA'] = $NVPRequestArray;
		$NVPResponseArray['RAWREQUEST'] = $NVPRequest;
		$NVPResponseArray['RAWRESPONSE'] = $NVPResponse;
								
		return $NVPResponseArray;
	}		
}