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
//ini_set("display_errors",1);
//error_reporting(E_ALL);
if (version_compare(PHP_VERSION, '5.3.1', '<'))
{
	die('Your host needs to use PHP 5.3.1 or higher to run this version of Joomla!');
}
if(!defined('_JEXEC'))
{
  define('_JEXEC', 1);
}
defined( '_JEXEC' ) or die( 'Restricted access' );
define('DS', DIRECTORY_SEPARATOR);
$dirname=str_ireplace(DS.'modules'.DS.'mod_paymybill'.DS.'return','', dirname(__FILE__));

if (file_exists($dirname.DS.'defines.php'))
{
	include_once $dirname.DS.'defines.php';
}
if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', $dirname);
	require_once JPATH_BASE.DS.'includes/defines.php';
}
require_once JPATH_BASE.DS.'includes/framework.php';
// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;
// Instantiate the application.
$app = JFactory::getApplication('site');

$lang = JFactory::getLanguage();
$extension = 'mod_paymybill';
$language_tag = $lang->getTag();
$reload = true;
$lang->load($extension, $dirname, $language_tag, $reload);

require_once(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."helper.php");
$PMBHelper = new PMBHelper();

$session_id=isset($_GET['order_id'])?$_GET['order_id']:"";

if($session_id=="")
  exit;
$module_id=0;
$tarr=explode("-",$session_id);
$module_id=$tarr[1]+0;
if($module_id==0)
  exit;

$order_paid=0;
$params=$PMBHelper->getWorkingModParams($module_id);
$vendor_name=trim($params['sagepay_vendor_name']);
$vendor_email=trim($params['sagepay_vendor_email']);
$pass=trim($params['sagepay_pass']);
$test_pass=trim($params['sagepay_test_pass']);
$sim_pass=trim($params['ssagepay_sim_pass']);
$mode=$params['sagepay_mode'];
$currency=$params['sagepay_currency'];
$encryption=$params['sagepay_encryption'];
$email_to=$params['sagepay_email_to'];
$protocol=$params['sagepay_protocol'];

require_once(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."methods".DS."sagepay.php");
$helper=new PMBSagePayHelper;

if($mode=='SIMULATOR')
{
  $strEncryptionPassword=$sim_pass;
}
elseif($mode=='TEST')
{
  $strEncryptionPassword=$test_pass;
}
else
{
  $strEncryptionPassword=$pass;
}

$strCrypt=isset($_GET["crypt"])?$_GET["crypt"]:"";
if($strCrypt=="")
  die('error');

$strDecoded=$helper->decodeAndDecrypt($strCrypt,$strEncryptionPassword);
$values = $helper->getToken($strDecoded);
// Split out the useful information into variables we can use
$strStatus=$values['Status'];
$strStatusDetail=$values['StatusDetail'];
$strVendorTxCode=$values["VendorTxCode"];
$strVPSTxId=$values["VPSTxId"];
$strTxAuthNo=$values["TxAuthNo"];
$strAmount=$values["Amount"];
$strAVSCV2=$values["AVSCV2"];
$strAddressResult=$values["AddressResult"];
$strPostCodeResult=$values["PostCodeResult"];
$strCV2Result=$values["CV2Result"];
$strGiftAid=$values["GiftAid"];
$str3DSecureStatus=$values["3DSecureStatus"];
//$strCAVV=$values["CAVV"];
$strCardType=$values["CardType"];
$strLast4Digits=$values["Last4Digits"];
//$strAddressStatus=$values["AddressStatus"]; // PayPal transactions only
//$strPayerStatus=$values["PayerStatus"];     // PayPal transactions only
//print_R($values);exit;
if($strStatus!="OK")
{
  $order_paid=0;
}
else
{
  $order_paid=1;
}


$method_name='SagePay';

$message="";
$message2="";
require_once(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."functions".DS."functions.php");
$pmbf=new PMBFunctions();
$details=$pmbf->getOrderDetails($session_id);
if($order_paid == 1)
{
  $pmbf->setOrderSuccess($params,$details,$method_name);
  //send mail
  if($params['mail_success']!=0)
  {
    $pmbf->sendConfirmationMail('success',$params,$details,$method_name);
  }
  $message='<h1><font color="green"><strong>'.JText::_( 'PMB_MODULE_ORDER_SUCCESS' ).'</strong></font></h1>';
  $message2='alert(\''.str_replace("'","\\'",JText::_( 'PMB_MODULE_ORDER_SUCCESS' )).'\');';
}
else
{
  $pmbf->setOrderCancelled($params,$details,$method_name);
  //send mail
  if($params['mail_cancelled']!=0)
  {
    $pmbf->sendConfirmationMail('cancelled',$params,$details,$method_name);
  }
  $message='<h1><font color="red"><strong>'.JText::_( 'PMB_MODULE_ORDER_CANCELED' ).'</strong></font></h1>';
  $message2='alert(\''.str_replace("'","\\'",JText::_( 'PMB_MODULE_ORDER_CANCELED' )).'\');';
}
$app->redirect(JRoute::_('index.php?Itemid=' . $params->get('successMenuItem', 101)));
