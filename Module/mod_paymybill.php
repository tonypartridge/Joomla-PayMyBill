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
if (!function_exists('getPMBDomain'))
{
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
}
if (!function_exists('setPMBModParams'))
{
function setPMBModParams($param_array,$module_id)
{
  if ( count($param_array) > 0 )
  {
    $db = JFactory::getDbo();
    $db->setQuery('SELECT params FROM #__modules WHERE id = '.$module_id);
    $params1 = json_decode( $db->loadResult(), true );
    foreach ( $param_array as $name => $value )
    {
      $params1[ (string) $name ] = (string) $value;
    }
    $params1String = json_encode( $params1 );
    $db->setQuery('UPDATE #__modules SET params = ' .
    $db->quote( $params1String ) .
    ' WHERE id = '.$module_id);
    $db->query();
  }
}
}
if (!function_exists('getPMBModParams'))
{
function getPMBModParams($module_id)
{
  $db = JFactory::getDbo();
  $db->setQuery('SELECT params FROM #__modules WHERE id = '.$module_id);
  $params1 = json_decode( $db->loadResult(), true );
  return $params1;
}
}
if (!function_exists('PMBModCheckLicense'))
{
function PMBModCheckLicense($lic,$module)
{
  $the_domain=getPMBDomain();
  if(!$the_domain)
  {
    return;
  }
  $checkurl='http://www.softpill.eu/check_complic.php?comp=paymybill&h='.$the_domain.'&lic='.$lic;
  if (!function_exists('curl_init'))
  {
    $output=@file_get_contents($checkurl);
    $the_domain=getPMBDomain();
    if($output==$the_domain)
    {
      $ok = 1;
    }
  }
  else
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $checkurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $output = curl_exec($ch);
    curl_close($ch);
    $the_domain=getPMBDomain();
    if($output==$the_domain)
    {
      $ok = 1;
    }
  }
  
  $application = JFactory::getApplication();
  if(@$ok==1)
  {
    $chk=md5($lic.$the_domain);
    setPMBModParams(array('dchk'=>$chk,'pmblic'=>$lic),$module->id);
    $application->enqueueMessage(JText::_( 'PMB_MODULE_LICENSE_OK' ), 'success');
  }
  else
  {
    setPMBModParams(array('pmblic'=>'','dchk'=>''),$module->id);
    $application->enqueueMessage(JText::_( 'PMB_MODULE_LICENSE_ERR_BAD' ), 'error');
  }
}
}
/*
$the_domain=getPMBDomain();
if($the_domain)
{
$params1  = getPMBModParams($module->id);
$lic=@$params1['pmblic'];
if($params->get('pmblic')!=$lic)
{
  setPMBModParams(array('pmblic'=>$params->get('pmblic')),$module->id);
  $lic=$params->get('pmblic');
}
$application = JFactory::getApplication();
if($lic=="")
{
  $application->enqueueMessage(JText::_( 'PMB_MODULE_LICENSE_ERR' ), 'error');
  setPMBModParams(array('pmblic'=>'','dchk'=>''),$module->id);
}
else
{
  $dchk=@$params1['dchk'];
  if($dchk=='')
  {
    PMBModCheckLicense($lic,$module);
  }
  else
  {
    $the_domain=getPMBDomain();
    $chk=md5($lic.$the_domain);
    if($chk!=$dchk && $the_domain)//check
    {
      //setPMBModParams(array('pmblic'=>'','dchk'=>''),$module->id);
      $application->enqueueMessage(JText::_( 'PMB_MODULE_LICENSE_ERR_BAD' ), 'error');
    }
  }
}
}
*/
require_once(dirname(__FILE__).DS.'helper.php');
require(JModuleHelper::getLayoutPath('mod_paymybill'));
?>