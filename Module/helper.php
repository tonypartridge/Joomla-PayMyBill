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
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
class PMBHelper
{
	function generate_random_letters($length)
  {
    $random = '';
    for ($i = 0; $i < $length; $i++) {
      $random .= chr(rand(ord('a'), ord('z')));
    }
    return $random;
  }
  private static function sortPaymentMethodsOrdering($a,$b)
  {
    if ($a['ordering'] == $b['ordering'])
    {
      return 0;
    }
    return ($a['ordering'] < $b['ordering']) ? -1 : 1;
  }
  function sortPaymentMethods($methods)
  {
    usort($methods, array('PMBHelper','sortPaymentMethodsOrdering'));
    return $methods;
  }
  function getWorkingModParams($module_id)
  {
    $db = JFactory::getDbo();
    $db->setQuery('SELECT params FROM #__modules WHERE id = '.$module_id);
    $params1 = json_decode( $db->loadResult(), true );
    return $params1;
  }
  function getNewOrderID($module_id)
  {
    $db=JFactory::getDBO();
    $order_id='pmb-'.$module_id.'-'.$this->generate_random_letters(6);
    $query="select order_id from #__paymybill where order_id='".$order_id."'";
    $db->setQuery($query);
    $obj=$db->loadObject();
    if(isset($obj->order_id))
    {
      return $this->getNewOrderID($module_id);
    }
    return $order_id;
  }
  function sendDebugMail($subj)
  {
    $body='';
    foreach($_GET as $key => $val)
    {
      $body.=$key.'='.$val.'<br />';
    }
    $body.='<br />';
    foreach($_POST as $key => $val)
    {
      $body.=$key.'='.$val.'<br />';
    }
    $body.='<br />';
    $mailer = JFactory::getMailer();
    $config = JFactory::getConfig();
    $sender = array( 
    $config->getValue( 'config.mailfrom' ),
    $config->getValue( 'config.fromname' ) );
    $mailer->setSender($sender);
    $mailer->addRecipient('mail@softpill.eu');
    $mailer->setSubject($subj);
    $mailer->isHTML(true);
    $mailer->setBody($body);
    $mailer->Send();
  }
}

?>