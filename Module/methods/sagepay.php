<?php
/**
 *
 * @package	    Pay My Bill Module
 * @subpackage	Pay My Bill Module
 * @version     1.0.0
 * @description Pay My Bill Module
 * @copyright	  Copyright � 2016 - All rights reserved.
 * @license		  GNU General Public License v2.0
 * @author		  SoftPill.Eu
 * @author mail	mail@softpill.eu
 * @website		  www.softpill.eu
 *
 */
//ini_set("display_errors",1);
//error_reporting(E_ALL);

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class PMBsagepay
{
  function checkMethod($params)
  {
    $vendor_missing=JText::_( 'PMB_SAGEPAY_MODULE_ERR_VENDOR' );
    $vendor_email_missing=JText::_( 'PMB_SAGEPAY_MODULE_ERR_EMAIL' );
    $pass_missing=JText::_( 'PMB_SAGEPAY_MODULE_ERR_PASS' );
    $test_pass_missing=JText::_( 'PMB_SAGEPAY_MODULE_ERR_TEST_PASS' );
    $sim_pass_missing=JText::_( 'PMB_SAGEPAY_MODULE_ERR_SIM_PASS' );
    
    $mode=trim($params->get('sagepay_mode'));
    $vendor_name=trim($params->get('sagepay_vendor_name'));
    $vendor_email=trim($params->get('sagepay_vendor_email'));
    $pass=trim($params->get('sagepay_pass'));
    $test_pass=trim($params->get('sagepay_test_pass'));
    $sim_pass=trim($params->get('ssagepay_sim_pass'));
    
    if($vendor_name=='')
    {
      ?>
        <div class="pmb_error"><?php echo $vendor_missing;?></div>
      <?php
      return false;
    }
    if($vendor_email=='')
    {
      ?>
        <div class="pmb_error"><?php echo $vendor_email_missing;?></div>
      <?php
      return false;
    }
    if($mode=='LIVE' && $pass=='')
    {
      ?>
        <div class="pmb_error"><?php echo $pass_missing;?></div>
      <?php
      return false;
    }
    if($mode=='TEST' && $test_pass=='')
    {
      ?>
        <div class="pmb_error"><?php echo $test_pass_missing;?></div>
      <?php
      return false;
    }
    if($mode=='SIMULATOR' && $sim_pass=='')
    {
      ?>
        <div class="pmb_error"><?php echo $sim_pass_missing;?></div>
      <?php
      return false;
    }
    
    return true;
  }
  function getPMBDomain()
  {
    $domain=str_ireplace("www.","",$_SERVER['HTTP_HOST']);
    $is_ip = ip2long($domain) !== false;
    if($is_ip)
    {
      return false;
    }
    else if(trim($domain)=='')
    {
      return false;
    }
    
    return $domain;
  }
  function doPayment($params,$details,$all_params)
  {
    /*
    $dchk=$all_params['dchk'];
    $lic=$all_params['pmblic'];
    $the_domain=$this->getPMBDomain();
    if($the_domain)
    {
      $chk=md5($lic.$the_domain);
      if($dchk!=$chk)
      {
        echo '<font color="red"><strong>'.JText::_( 'PMB_MODULE_LICENSE_ERR' ).'</strong></font>';
        exit;
      }
    }
    */
    $product_name=$params->get('product_name');
    $invoice_field=$params->get('show_inv_field');
    $cost_per_transaction=$params->get('sagepay_trans_cost')+0;
    $percent_per_transaction=$params->get('sagepay_trans_percent_cost')+0;
    
    $vendor_name=trim($params->get('sagepay_vendor_name'));
    $vendor_email=trim($params->get('sagepay_vendor_email'));
    $pass=trim($params->get('sagepay_pass'));
    $test_pass=trim($params->get('sagepay_test_pass'));
    $sim_pass=trim($params->get('ssagepay_sim_pass'));
    $mode=$params->get('sagepay_mode');
    $currency=$params->get('sagepay_currency');
    $encryption=$params->get('sagepay_encryption');
    $email_to=$params->get('sagepay_email_to');
    $protocol=$params->get('sagepay_protocol');
    
    $details['subtotal']=$details['payment'];
    $payment=$details['payment'];
    if($cost_per_transaction>0)
    {
      $payment+=$cost_per_transaction;
    }
    if($percent_per_transaction>0)
    {
      $payment=$payment+($payment*$percent_per_transaction);
    }
    
    $payment=round($payment,2);
    $payment=number_format($payment,2,'.','');
    $details['payment']=$payment;
    $details['mode']=$mode;
    $details['currency']=$currency;
    require_once(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."functions".DS."functions.php");
    $pmbf=new PMBFunctions();
    if($params->get('mail_pending')!=0)
    {
      
      $pmbf->sendConfirmationMail("pending",$all_params,$details,'SagePay');//send pending mail
    }
    $pmbf->saveOrder($payment,$mode,$all_params,$details,'SagePay');
    $helper=new PMBSagePayHelper;
    
    $PaymentDescription=$product_name.' at '.$_SERVER['HTTP_HOST'].' order.';
    
    if($invoice_field)
    {
      $PaymentDescription=$product_name.' at '.$_SERVER['HTTP_HOST'].' for '.$details['InvNr'];
    }
    
    $strPost="VendorTxCode=" . $details['order_id'];
    $strVendorName=$vendor_name;
    $strPost=$strPost . "&Amount=" . $payment;
    $strPost=$strPost . "&Currency=" . $currency;
    $strPost=$strPost . "&Description=".$PaymentDescription;
    $strPost=$strPost . "&SuccessURL=" . JROUTE::_(JURI::root()."modules/mod_paymybill/return/sagepay_return.php?order_id=".$details['order_id']);
    $strPost=$strPost . "&FailureURL=" . JROUTE::_(JURI::root()."modules/mod_paymybill/return/sagepay_cancel.php?order_id=".$details['order_id']);
    $strPost=$strPost . "&CustomerEMail=" . $details['BillEmail'];
    $strPost=$strPost . "&BillingPhone=" . $details['BillPhone'];
    
    if ($email_to == 0)
    $strPost=$strPost . "&SendEMail=0";
    else {
    if ($email_to == 1) {
    	$strPost=$strPost . "&SendEMail=1";
    } else {
    	$strPost=$strPost . "&SendEMail=2";
    }
    if ($vendor_email <> "")
  	    $strPost=$strPost . "&VendorEMail=" . $vendor_email;
    }
    
    $strPost=$strPost . "&BillingFirstnames=" . $details['BillFname'];
    $strPost=$strPost . "&BillingSurname=" . $details['BillLname'];
    $strPost=$strPost . "&CustomerName=" . $details['BillFname']." ".$details['BillLname'];
    $strPost=$strPost . "&BillingAddress1=" . $details['BillAddr'];
    $strPost=$strPost . "&BillingCity=" . $details['BillCity'];
    $strPost=$strPost . "&BillingPostCode=" . $details['BillZip'];
    $strPost=$strPost . "&BillingCountry=" . $details['BillCountry'];
    
    if($details['BillCountry']=='US')
    {
      $strPost=$strPost . "&BillingState=" . $details['BillState'];
      $strPost=$strPost . "&DeliveryState=" . $details['BillState'];
    }
    
    $strPost=$strPost . "&DeliveryFirstnames=" . $details['BillFname'];
    $strPost=$strPost . "&DeliverySurname=" . $details['BillLname'];
    $strPost=$strPost . "&DeliveryAddress1=" . $details['BillAddr'];
    $strPost=$strPost . "&DeliveryCity=" . $details['BillCity'];
    $strPost=$strPost . "&DeliveryPostCode=" . $details['BillZip'];
    $strPost=$strPost . "&DeliveryCountry=" . $details['BillCountry'];
    if($product_name=='')
      $product_name='My Product';
    
    if($invoice_field)
    {
      $strPost=$strPost . "&Basket=2:" .$product_name.':1:-:-:'.$payment.':'.$payment.":INV-".strtoupper($details['InvNr']).":1:-:-:0:0";
    }
    else
    {
      $strPost=$strPost . "&Basket=1:" .$product_name.':1:-:-:'.$payment.':'.$payment;
    }
    $strCrypt="";
    $url='';
    if($mode=="LIVE")
    {
    	$url="https://live.sagepay.com/gateway/service/vspform-register.vsp";
    	$strCrypt = $helper->encryptAndEncode($strPost,$encryption,$pass);
    } 
    elseif($mode=="TEST")
    {
    	$url="https://test.sagepay.com/gateway/service/vspform-register.vsp";
    	$strCrypt = $helper->encryptAndEncode($strPost,$encryption,$test_pass);
    }
    else
    {
    	$url="https://test.sagepay.com/simulator/vspformgateway.asp";
    	$strCrypt = $helper->encryptAndEncode($strPost,$encryption,$sim_pass);
    }
    $pmb_button='Redirecting to payment page';
    ob_end_clean();
    $html = '<html><head><title>Redirection</title></head><body><div style="margin: auto; text-align: center;">';
		$html .= '<form action="'.$url.'" method="POST" id="SagePayForm" name="SagePayForm" style="display:none;">';
		$html .= '<input type="hidden" name="navigate" value="" />';
		$html .= '<input type="hidden" name="VPSProtocol" value="'.$protocol.'">';
		$html .= '<input type="hidden" name="TxType" value="PAYMENT">';
		$html .= '<input type="hidden" name="Vendor" value="'.$vendor_name.'">';
		$html .= '<input type="hidden" name="Crypt" value="'.$strCrypt.'">';
		$html.= '<input type="submit" class="ssp_submit" value="'.$pmb_button.'" />';
		$html.= '</form></div>';
		//$html.=htmlentities($strPost);
		$html.= ' <script type="text/javascript">';
		$html.= ' document.SagePayForm.submit();';
		$html.= ' </script></body></html>';
    
    echo $html;exit;
  }
}






















class PMBSagePayHelper
{
	/**
	 * Do something getItems method
	 *
	 * @param 	
	 * @return
	 */
    public function getItems($params)
    {
      // Your custom code here
    } //end getItems
    function generate_random_letters($length) {
      $random = '';
      for ($i = 0; $i < $length; $i++) {
        $random .= chr(rand(ord('a'), ord('z')));
      }
      return $random;
    }
    /* The getToken function.                                                                                         **
    ** NOTE: A function of convenience that extracts the value from the "name=value&name2=value2..." reply string **
    ** Works even if one of the values is a URL containing the & or = signs.                                      	  */
    function getToken($thisString) {
    
      // List the possible tokens
      $Tokens = array(
        "Status",
        "StatusDetail",
        "VendorTxCode",
        "VPSTxId",
        "TxAuthNo",
        "Amount",
        "AVSCV2", 
        "AddressResult", 
        "PostCodeResult", 
        "CV2Result", 
        "GiftAid", 
        "3DSecureStatus", 
        "CAVV",
    	"AddressStatus",
    	"CardType",
    	"Last4Digits",
    	"PayerStatus");
    
      // Initialise arrays
      $output = array();
      $resultArray = array();
      
      // Get the next token in the sequence
      for ($i = count($Tokens)-1; $i >= 0 ; $i--){
        // Find the position in the string
        $start = strpos($thisString, $Tokens[$i]);
    	// If it's present
        if ($start !== false){
          // Record position and token name
          @$resultArray[$i]->start = $start;
          $resultArray[$i]->token = $Tokens[$i];
        }
      }
      
      // Sort in order of position
      sort($resultArray);
    	// Go through the result array, getting the token values
      for ($i = 0; $i<count($resultArray); $i++){
        // Get the start point of the value
        $valueStart = $resultArray[$i]->start + strlen($resultArray[$i]->token) + 1;
    	// Get the length of the value
        if ($i==(count($resultArray)-1)) {
          $output[$resultArray[$i]->token] = substr($thisString, $valueStart);
        } else {
          $valueLength = $resultArray[$i+1]->start - $resultArray[$i]->start - strlen($resultArray[$i]->token) - 2;
    	  $output[$resultArray[$i]->token] = substr($thisString, $valueStart, $valueLength);
        }      
    
      }
    
      // Return the ouput array
      return $output;
    }
    // Filters unwanted characters out of an input string based on type.  Useful for tidying up FORM field inputs
    //   Parameter strRawText is a value to clean.
    //   Parameter filterType is a value from one of the CLEAN_INPUT_FILTER_ constants.
    function cleanInput($strRawText, $filterType)
    {
        $strAllowableChars = "";
        $blnAllowAccentedChars = FALSE;
        $strCleaned = "";
        $filterType = strtolower($filterType); //ensures filterType matches constant values
        
        if ($filterType == CLEAN_INPUT_FILTER_TEXT)
        { 
            $strAllowableChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 .,'/\\{}@():?-_&£$=%~*+\"\n\r";
            $strCleaned = $this->cleanInput2($strRawText, $strAllowableChars, TRUE);
    	}
        elseif ($filterType == CLEAN_INPUT_FILTER_NUMERIC) 
        {
            $strAllowableChars = "0123456789 .,";
            $strCleaned = $this->cleanInput2($strRawText, $strAllowableChars, FALSE);
        }   
        elseif ($filterType == CLEAN_INPUT_FILTER_ALPHABETIC || $filterType == CLEAN_INPUT_FILTER_ALPHABETIC_AND_ACCENTED)
    	{
            $strAllowableChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz";
            if ($filterType == CLEAN_INPUT_FILTER_ALPHABETIC_AND_ACCENTED) $blnAllowAccentedChars = TRUE;
            $strCleaned = $this->cleanInput2($strRawText, $strAllowableChars, $blnAllowAccentedChars);
    	}
        elseif ($filterType == CLEAN_INPUT_FILTER_ALPHANUMERIC || $filterType == CLEAN_INPUT_FILTER_ALPHANUMERIC_AND_ACCENTED)
    	{
            $strAllowableChars = "0123456789 ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            if ($filterType == CLEAN_INPUT_FILTER_ALPHANUMERIC_AND_ACCENTED) $blnAllowAccentedChars = TRUE;
            $strCleaned = $this->cleanInput2($strRawText, $strAllowableChars, $blnAllowAccentedChars);
    	}
        else // Widest Allowable Character Range
        {
            $strAllowableChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 .,'/\\{}@():?-_&£$=%~*+\"\n\r";
            $strCleaned = $this->cleanInput2($strRawText, $strAllowableChars, TRUE);
        }
        
        return $strCleaned;
    }
    // Filters unwanted characters out of an input string based on an allowable character set.  Useful for tidying up FORM field inputs
    //   Parameter strRawText is a value to clean.
    //   Parameter "strAllowableChars" is a string of characters allowable in "strRawText" if its to be deemed valid.
    //   Parameter "blnAllowAccentedChars" accepts a boolean value which determines if "strRawText" can contain Accented or High-order characters.
    function cleanInput2($strRawText, $strAllowableChars, $blnAllowAccentedChars)
    {
        $iCharPos = 0;
        $chrThisChar = "";
        $strCleanedText = "";
        
        //Compare each character based on list of acceptable characters
        while ($iCharPos < strlen($strRawText))
        {
            // Only include valid characters **
            $chrThisChar = substr($strRawText, $iCharPos, 1);
            if (strpos($strAllowableChars, $chrThisChar) !== FALSE)
            {
                $strCleanedText = $strCleanedText . $chrThisChar;
            }
            elseIf ($blnAllowAccentedChars == TRUE)
            {
                // Allow accented characters and most high order bit chars which are harmless **
                if (ord($chrThisChar) >= 191)
                {
                	$strCleanedText = $strCleanedText . $chrThisChar;
                }
            }
            
            $iCharPos = $iCharPos + 1;
        }
        
        return $strCleanedText;
    }
    /* Base 64 Encoding function **
    ** PHP does it natively but just for consistency and ease of maintenance, let's declare our own function **/
    function base64Encode($plain) {
      // Initialise output variable
      $output = "";
      
      // Do encoding
      $output = base64_encode($plain);
      
      // Return the result
      return $output;
    }
    /* Base 64 decoding function **
    ** PHP does it natively but just for consistency and ease of maintenance, let's declare our own function **/
    function base64Decode($scrambled) {
      // Initialise output variable
      $output = "";
      
      // Fix plus to space conversion issue
      $scrambled = str_replace(" ","+",$scrambled);
      
      // Do encoding
      $output = base64_decode($scrambled);
      
      // Return the result
      return $output;
    }
    /*  The SimpleXor encryption algorithm                                                                                **
    **  NOTE: This is a placeholder really.  Future releases of Form will use AES or TwoFish.  Proper encryption          **
    **  This simple function and the Base64 will deter script kiddies and prevent the "View Source" type tampering        **
    **  It won't stop a half decent hacker though, but the most they could do is change the amount field to something     **
    **  else, so provided the vendor checks the reports and compares amounts, there is no harm done.  It's still          **
    **  more secure than the other PSPs who don't both encrypting their forms at all                                      */
    
    function simpleXor($InString, $Key) {
      // Initialise key array
      $KeyList = array();
      // Initialise out variable
      $output = "";
      
      // Convert $Key into array of ASCII values
      for($i = 0; $i < strlen($Key); $i++){
        $KeyList[$i] = ord(substr($Key, $i, 1));
      }
    
      // Step through string a character at a time
      for($i = 0; $i < strlen($InString); $i++) {
        // Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
        // % is MOD (modulus), ^ is XOR
        $output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
      }
    
      // Return the result
      return $output;
    }
    
    function getParams($module_id)
    {
      jimport('joomla.version');
      $jver = new JVersion();
      $jversion=$jver->getShortVersion();
      $joomla_ver=2;
      if(version_compare($jversion,'2.5','<'))
      {
        $joomla_ver=1;
      }
      if($joomla_ver==1)
      {
        $params1=array();
        $db = JFactory::getDbo();
        $db->setQuery('SELECT params FROM #__modules WHERE id = '.$module_id);
        $params=$db->loadResult();
        
        if($params!="")
        {
          $tarr=explode("\n",$params);
        }
        if(count($tarr)>0)
        {
          foreach($tarr as $p)
          {
            if($p!="")
            {
              $pos = strpos($p, "=");
              if ($pos === false) {}
              else
              {
                $key=substr($p,0,$pos);
                $val=substr($p,($pos+1),strlen($p));
                $params1[$key]=$val;
              }
            }
          }
        }
        return $params1;
      }
      else
      {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT params FROM #__modules WHERE id = '.$module_id);
        $params1 = json_decode( $db->loadResult(), true );
        return $params1;
      }
    }
    
    public function _removePKCS5Padding($decrypted) {
  		$padChar = ord($decrypted[strlen($decrypted) - 1]);
  	    return substr($decrypted, 0, -$padChar);
  	}
  
  	public function _addPKCS5Padding($input) {
  	   $blocksize = 16;
  	   $padding = "";
  
  	   // Pad input to an even block size boundary
  	   $padlength = $blocksize - (strlen($input) % $blocksize);
  	   for($i = 1; $i <= $padlength; $i++) {
  	      $padding .= chr($padlength);
  	   }
  
  	   return $input . $padding;
  	}
    
    //** Wrapper function do encrypt an encode based on strEncryptionType setting **
    function encryptAndEncode($strIn,$strEncryptionType,$strEncryptionPassword) {
      
      if(function_exists('openssl_encrypt')) {
  			return "@" . strtoupper(bin2hex(openssl_encrypt($strIn, 'AES-128-CBC', $strEncryptionPassword, OPENSSL_RAW_DATA, $strEncryptionPassword)));
  		} else {
  			$strIn = $this->_addPKCS5Padding($strIn);
  			return "@" . strtoupper(bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $strEncryptionPassword, $strIn, MCRYPT_MODE_CBC, $strEncryptionPassword)));
  		}
      /*
    	if ($strEncryptionType=="XOR") 
    	{
    		//** XOR encryption with Base64 encoding **
    		return $this->base64Encode($this->simpleXor($strIn,$strEncryptionPassword));
    	} 
    	else 
    	{
    		//** AES encryption, CBC blocking with PKCS5 padding then HEX encoding - DEFAULT **
    
    		//** use initialization vector (IV) set from $strEncryptionPassword
        	$strIV = $strEncryptionPassword;
        	
        	//** add PKCS5 padding to the text to be encypted
        	$strIn = $this->addPKCS5Padding($strIn);
    
        	//** perform encryption with PHP's MCRYPT module
    		$strCrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $strEncryptionPassword, $strIn, MCRYPT_MODE_CBC, $strIV);
    		
    		//** perform hex encoding and return
    		return "@" . bin2hex($strCrypt);
    	}
      */
    }
    //** Wrapper function do decode then decrypt based on header of the encrypted field **
    function decodeAndDecrypt($strIn,$strEncryptionPassword) {
    	
      $strIn = substr($strIn,1);
      	$strIn = pack('H*', $strIn);
  		if(function_exists('openssl_decrypt')) {
  			return openssl_decrypt($strIn, 'AES-128-CBC', $strEncryptionPassword, OPENSSL_RAW_DATA, $strEncryptionPassword);
  		} else {
  			return $this->_removePKCS5Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $strEncryptionPassword, $strIn, MCRYPT_MODE_CBC, $strEncryptionPassword));
  		}
      /*
    	if (substr($strIn,0,1)=="@") 
    	{
    		//** HEX decoding then AES decryption, CBC blocking with PKCS5 padding - DEFAULT **
    		
    		//** use initialization vector (IV) set from $strEncryptionPassword
        	$strIV = $strEncryptionPassword;
        	
        	//** remove the first char which is @ to flag this is AES encrypted
        	$strIn = substr($strIn,1); 
        	
        	//** HEX decoding
        	$strIn = pack('H*', $strIn);
        	
        	//** perform decryption with PHP's MCRYPT module
    		return $this->removePKCS5Padding(
    			mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $strEncryptionPassword, $strIn, MCRYPT_MODE_CBC, $strIV)); 
    	} 
    	else 
    	{
    		//** Base 64 decoding plus XOR decryption **
    		return $this->simpleXor($this->base64Decode($strIn),$strEncryptionPassword);
    	}
      */
    }
    
    // New function added 2011-12-29 
    // Need to remove padding bytes from end of decoded string
    function removePKCS5Padding($decrypted) {
    	$padChar = ord($decrypted[strlen($decrypted) - 1]);
        return substr($decrypted, 0, -$padChar); 
    }
    
    //** PHP's mcrypt does not have built in PKCS5 Padding, so we use this
    function addPKCS5Padding($input)
    {
       $blocksize = 16;
       $padding = "";
    
       // Pad input to an even block size boundary
       $padlength = $blocksize - (strlen($input) % $blocksize);
       for($i = 1; $i <= $padlength; $i++) {
          $padding .= chr($padlength);
       }
       
       return $input . $padding;
    }
    
    // Inspects and validates user input for a name field. Returns TRUE if input value is valid as a name field.
    //   Parameter "strInputValue" is the field value to validate.
    //   Parameter "returnedResult" sets a result to a value from the list of field validation constants beginning with "FIELD_".
    function isValidNameField($strInputValue, &$returnedResult)
    {
        $strAllowableChars = " ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-.'&\\";
        $strInputValue = trim($strInputValue);
        $returnedResult = $this->validateString($strInputValue, $strAllowableChars, TRUE, TRUE, 20, -1);
        if ($returnedResult == FIELD_VALID) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    // Inspects and validates user input for an Address field.
    //   Parameter "blnIsRequired" specifies whether "strInputValue" must have a non-null and non-empty value.
    function isValidAddressField($strInputValue, $blnIsRequired, &$returnedResult )
    {
        $strAllowableChars = " 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-.',/\\()&:+\n\r";
        $strInputValue = trim($strInputValue);
        $returnedResult = $this->validateString($strInputValue, $strAllowableChars, TRUE, $blnIsRequired, 100, -1);
    
        if ($returnedResult == FIELD_VALID) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    // Inspects and validates user input for a City field.
    function isValidCityField($strInputValue, &$returnedResult)
    {
        $strAllowableChars = " 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-.',/\\()&:+\n\r";
        $strInputValue = trim($strInputValue);
        $returnedResult = $this->validateString($strInputValue, $strAllowableChars, TRUE, TRUE, 40, -1);
    
        if ($returnedResult == FIELD_VALID) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    // Inspects and validates user input for a Postcode/zip field. 
    function isValidPostcodeField($strInputValue, &$returnedResult)
    {
        $strAllowableChars = " 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-";
        $strInputValue = trim($strInputValue);
        $returnedResult = $this->validateString($strInputValue, $strAllowableChars, FALSE, TRUE, 10, -1);
    
        if ($returnedResult == FIELD_VALID) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    // Inspects and validates user input for an email field. 
    function isValidEmailField($strInputValue, &$returnedResult)
    {
        // The allowable e-mail address format accepted by the SagePay gateway must be RFC 5321/5322 compliant (see RFC 3696) 
    	$sEmailRegExpPattern = '/^[a-z0-9\xC0-\xFF\!#$%&amp;\'*+\/=?^_`{|}~\*-]+(?:\.[a-z0-9\xC0-\xFF\!#$%&amp;\'*+\/=?^_`{|}~*-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[a-z]{2,3}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|at|coop|travel)$/';
        $strInputValue = trim($strInputValue);
        $returnedResult = $this->validateStringWithRegExp($strInputValue, $sEmailRegExpPattern, FALSE);
        
        if ($returnedResult == FIELD_VALID) {
            return TRUE;
        } else{
            return FALSE;
        }
    }
    
    
    // Inspects and validates user input for a phone field. 
    function isValidPhoneField($strInputValue, &$returnedResult)
    {
        $strAllowableChars = " 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-()+";
        $strInputValue = trim($strInputValue);
        $returnedResult = $this->validateString($strInputValue, $strAllowableChars, FALSE, FALSE, 20, -1);
    
        if ($returnedResult == FIELD_VALID) {
            return TRUE;
        } else{
            return FALSE;
        }
    }
    
    
    // A generic function used to inspect and validate a string from user input.
    //   Parameter "strInputValue" is the value to perform validation on.
    //   Parameter "strAllowableChars" is a string of characters allowable in "strInputValue" if its to be deemed valid.
    //   Parameter "blnAllowAccentedChars" accepts a boolean value which determines if "strInputValue" can contain Accented or High-order characters.
    //   Parameter "blnIsRequired" accepts a boolean value which specifies whether "strInputValue" must have a non-null and non-empty value.
    //   Parameter "intMaxLength" accepts an integer which specifies the maximum allowable length of "strInputValue". Set to -1 for this to be ignored.
    //   Parameter "intMinLength" specifies the miniumum allowable length of "strInputValue". Set to -1 for this to be ignored.
    //   Returns a result from one of the field validation constants that begin with "FIELD_" 
    function validateString($strInputValue, $strAllowableChars, $blnAllowAccentedChars, $blnIsRequired, $intMaxLength, $intMinLength)
    {
        if ($blnIsRequired == TRUE && strlen($strInputValue) == 0)
    	{
            return FIELD_INVALID_REQUIRED_INPUT_VALUE_MISSING;
    	}
        elseif (($intMaxLength != -1) && (strlen($strInputValue) > $intMaxLength)) 
    	{
            return FIELD_INVALID_MAXIMUM_LENGTH_EXCEEDED;
    	}
        elseif ($strInputValue != cleanInput2($strInputValue, $strAllowableChars, $blnAllowAccentedChars))
    	{
            return FIELD_INVALID_BAD_CHARACTERS;
    	}
        elseif (($blnIsRequired == TRUE) && (strlen($strInputValue) < $intMinLength)) 
    	{
            return FIELD_INVALID_MINIMUM_LENGTH_NOT_MET;
    	}
        elseif (($blnIsRequired == FALSE) && (strlen($strInputValue) > 0) && (strlen($strInputValue) < $intMinLength))
    	{
            return FIELD_INVALID_MINIMUM_LENGTH_NOT_MET;
        }
        else
        {
            return FIELD_VALID;
        }
    }
    
    
    // A generic function to inspect and validate a string from user input based on a Regular Expression pattern.
    //   Parameter "strInputValue" is the value to perform validation on.
    //   Parameter "strRegExPattern" is a Regular Expression string pattern used to validate against "strInputValue".
    //   Parameter "blnIsRequired" accepts a boolean value which specifies whether "strInputValue" must have a non-null and non-empty value.
    //   Returns a result from one of the field validation constants that begin with "FIELD_" 
    function validateStringWithRegExp($strInputValue, $strRegExPattern, $blnIsRequired)
    {
        if ($blnIsRequired == TRUE && strlen($strInputValue) == 0) 
        {
            return FIELD_INVALID_REQUIRED_INPUT_VALUE_MISSING;
    	}
        elseif (strlen($strInputValue) > 0)
        {    
            if (preg_match($strRegExPattern, $strInputValue)) {
                return FIELD_VALID;
            } else {
                return FIELD_INVALID_BAD_FORMAT;
            }
        }
        else 
        {
            return FIELD_VALID;
        }
    }
    
    
    // Maps a Field Validation constant value to a string representing a user friendly validation error message.
    //   Parameter "strFieldLabelName" is the display name of the form field to use in the returned message.
    function getValidationMessage($fieldValidationCode, $strFieldLabelName)
    {
        $strReturn = "";
    
        switch ($fieldValidationCode)
    	{
            case FIELD_INVALID_BAD_CHARACTERS:
                $strReturn = "Please correct " . $strFieldLabelName . " as it contains disallowed characters.";
    			break;
            case FIELD_INVALID_BAD_FORMAT:
                $strReturn = "Please correct " . $strFieldLabelName . " as the format is invalid.";
    			break;
            case FIELD_INVALID_MINIMUM_LENGTH_NOT_MET:
                $strReturn = "Please correct " . $strFieldLabelName . " as the value is not long enough.";
    			break;
            case FIELD_INVALID_MAXIMUM_LENGTH_EXCEEDED:
                $strReturn = "Please correct " . $strFieldLabelName . " as the value is too long.";
    			break;
            case FIELD_INVALID_REQUIRED_INPUT_VALUE_MISSING:
                $strReturn = "Please enter a value for " . $strFieldLabelName . " where requested below.";
    			break;
            case FIELD_INVALID_REQUIRED_INPUT_VALUE_NOT_SELECTED:
                $strReturn = "Please select a value for " . $strFieldLabelName . " where requested below.";
    			break;
        }
    
        return $strReturn;
    }
}
?>