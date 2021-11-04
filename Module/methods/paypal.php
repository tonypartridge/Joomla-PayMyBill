<?php
/**
 *
 * @package	    Pay My Bill Module
 * @subpackage	Pay My Bill Module
 * @version     1.0.0
 * @description Pay My Bill Module
 * @copyright	  Copyright © 2016 - All rights reserved.
 * @license		  GNU General Public License v2.0
 * @author		  SoftPill.Eu
 * @author mail	mail@softpill.eu
 * @website		  www.softpill.eu
 *
 */
//ini_set("display_errors",0);
//error_reporting(E_ALL);

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class PMBpaypal
{
  function checkMethod($params)
  {
    $vendor_missing=JText::_( 'PMB_PAYPAL_MODULE_ERR_VENDOR' );
    $vendor2_missing=JText::_( 'PMB_PAYPAL_MODULE_ERR_VENDOR2' );
    $vendor_name=trim($params->get('paypal_user'));
    $vendor_name2=trim($params->get('paypal_user2'));
    $mode=trim($params->get('paypal_mode'));
    if($vendor_name=='' && $mode=='LIVE')
    {
      ?>
        <div class="pmb_error"><?php echo $vendor_missing;?></div>
      <?php
      return false;
    }
    if($vendor_name2=='' && $mode=='TEST')
    {
      ?>
        <div class="pmb_error"><?php echo $vendor2_missing;?></div>
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
    $cost_per_transaction=$params->get('paypal_trans_cost')+0;
    $percent_per_transaction=$params->get('paypal_trans_percent_cost')+0;
    
    $vendor_name=trim($params->get('paypal_user'));
    $vendor_name2=trim($params->get('paypal_user2'));
    $language=$params->get('paypal_language');
    $mode=$params->get('paypal_mode');
    $currency=$params->get('paypal_currency');
    
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
    $payment=number_format($payment,2);
    $details['payment']=$payment;
    $details['mode']=$mode;
    $details['currency']=$currency;
    if($params->get('mail_pending')!=0)
    {
      require_once(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."functions".DS."functions.php");
      $pmbf=new PMBFunctions();
      $pmbf->sendConfirmationMail("pending",$all_params,$details,'PayPal');//send pending mail
    }
    $pmbf->saveOrder($payment,$mode,$all_params,$details,'PayPal');
    
    $url='';
    if($mode=='LIVE')
    {
      $merchant_email=$vendor_name;
      $url='https://www.paypal.com/cgi-bin/webscr';
    }
    else
    {
      $merchant_email=$vendor_name2;
      $url='https://www.sandbox.paypal.com/cgi-bin/webscr';
    }
    
    $PaymentDescription=$product_name.' at '.$_SERVER['HTTP_HOST'].' order.';
    
    if($invoice_field)
    {
      $PaymentDescription=$product_name.' at '.$_SERVER['HTTP_HOST'].' for '.$details['InvNr'];
    }
    
    $post_variables = Array(
		'cmd'              => '_ext-enter',
		'redirect_cmd'     => '_xclick',
		'upload'           => '1', 
		'business'         => $merchant_email,
		'receiver_email'   => $merchant_email,
		'order_number'     => $details['order_id'],
		"invoice"          => $details['order_id'],
		'item_name'        => $PaymentDescription,
		"amount"           => $payment,
		"currency_code"    => $currency,
		"address_override" => '0',
		"first_name"       => $details['BillFname'],
		"last_name"        => $details['BillLname'],
		"address1"         => $details['BillAddr'],
		"address2"         => '',
		"zip"              => $details['BillZip'],
		"city"             => $details['BillCity'],
		"state"            => $details['BillState'],
		"country"          => $details['BillCountry'],
		"email"            => $details['BillEmail'],
		"night_phone_b"    => $details['BillPhone'],
		"return"           => JROUTE::_(JURI::root()."modules/mod_paymybill/return/paypal_return.php"),
		"notify_url"       => JROUTE::_(JURI::root()."modules/mod_paymybill/return/paypal_notify.php"),
		"cancel_return"    => JROUTE::_(JURI::root()."modules/mod_paymybill/return/paypal_cancel.php?order_id=".$details['order_id']),
		"rm"               => '2',
		"no_shipping"      => '0',
		"no_note"          => '1',
    "lc"               => $language
    );

    $the_url=$url;
    
    $pmb_button='Redirecting to payment page';
    
    $html = '<html><head><title>Redirection</title></head><body><div style="margin: auto; text-align: center;">';
		$html.= "\n".'<form name="PMBfrm" id="PMBfrm" action="'.$the_url.'" method="post">';
	  foreach ($post_variables as $name => $value) {
			$html .= '
      <input type="hidden" name="' . $name . '" value="' . htmlspecialchars ($value) . '" />';
		}
		$html.= '<input type="submit"  value="'.$pmb_button.'" />';
		$html.= '</form></div>';
		$html.= ' <script type="text/javascript">';
		$html.= ' document.PMBfrm.submit();';
		$html.= ' </script></body></html>';
    
    ob_end_clean();
    
    echo $html;exit;
  }
}
?>