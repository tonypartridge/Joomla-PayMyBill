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

$session_id=isset($_POST['invoice'])?$_POST['invoice']:"";

if($session_id=="")
  exit;
$module_id=0;
$tarr=explode("-",$session_id);
$module_id=$tarr[1]+0;
if($module_id==0)
  exit;

$order_paid=0;
$params=$PMBHelper->getWorkingModParams($module_id);

if (strcmp ($_POST['payment_status'], 'Completed') == 0) {
  if($params['paypal_mode']=='LIVE')
  {
    $verify_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_notify-validate&' . http_build_query( $_POST );  
  }
  else
  {
    $verify_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_notify-validate&' . http_build_query( $_POST );  
  }
  
  if( !strstr( file_get_contents( $verify_url ), 'VERIFIED' ) )
  {
    $order_paid=0;
  }
  else
  {
	  $order_paid=1;
  }
}

$method_name='PayPal';

$message="";
$message2="";
require_once(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."functions".DS."functions.php");
$pmbf=new PMBFunctions();
$details=$pmbf->getOrderDetails($session_id);
if($order_paid == 1)
{
  //send mail
  if($params['mail_success']!=0)
  {
    $pmbf->sendConfirmationMail('success',$params,$details,$method_name);
  }
  $pmbf->setOrderSuccess($params,$details,$method_name);
}
else
{
  //send mail
  if($params['mail_cancelled']!=0)
  {
    $pmbf->sendConfirmationMail('cancelled',$params,$details,$method_name);
  }
  $pmbf->setOrderCancelled($params,$details,$method_name);
}
exit;
?>
