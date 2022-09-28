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
// no direct access
if(!defined('_JEXEC'))
{
  define('_JEXEC', 1);
}
defined( '_JEXEC' ) or die( 'Restricted access' );
//ini_set("display_errors",0);
//error_reporting(E_ALL);
class PMBFunctions
{
  function sendConfirmationMail($type,$params,$details,$payment_method)
  {
    if($type=='pending' && $params['mail_pending']==0)
    {
      return;
    }
    if($type=='success' && $params['mail_success']==0)
    {
      return;
    }
    if($type=='cancelled' && $params['mail_cancelled']==0)
    {
      return;
    }
    $country_codes_arr=array('AF'=>'Afghanistan', 'AL'=>'Albania', 'DZ'=>'Algeria', 'AS'=>'American Samoa', 'AD'=>'Andorra', 'AO'=>'Angola', 'AI'=>'Anguilla', 'AQ'=>'Antarctica', 'AG'=>'Antigua and Barbuda', 'AR'=>'Argentina', 'AM'=>'Armenia', 'AW'=>'Aruba', 'AU'=>'Australia', 'AT'=>'Austria', 'AZ'=>'Azerbaijan', 'BS'=>'Bahamas', 'BH'=>'Bahrain', 'BD'=>'Bangladesh', 'BB'=>'Barbados', 'BY'=>'Belarus', 'BE'=>'Belgium', 'BZ'=>'Belize', 'BJ'=>'Benin', 'BM'=>'Bermuda', 'BT'=>'Bhutan', 'BO'=>'Bolivia', 'BA'=>'Bosnia and Herzegowina', 'BW'=>'Botswana', 'BV'=>'Bouvet Island', 'BR'=>'Brazil', 'IO'=>'British Indian Ocean Territory', 'BN'=>'Brunei Darussalam', 'BG'=>'Bulgaria', 'BF'=>'Burkina Faso', 'BI'=>'Burundi', 'KH'=>'Cambodia', 'CM'=>'Cameroon', 'CA'=>'Canada', 'XC'=>'Canary Islands', 'CV'=>'Cape Verde', 'KY'=>'Cayman Islands', 'CF'=>'Central African Republic', 'TD'=>'Chad', 'CL'=>'Chile', 'CN'=>'China', 'CX'=>'Christmas Island', 'CC'=>'Cocos (Keeling) Islands', 'CO'=>'Colombia', 'KM'=>'Comoros', 'CG'=>'Congo', 'CK'=>'Cook Islands', 'CR'=>'Costa Rica', 'CI'=>'Cote D\'Ivoire', 'HR'=>'Croatia', 'CU'=>'Cuba', 'CY'=>'Cyprus', 'CZ'=>'Czech Republic', 'DK'=>'Denmark', 'DJ'=>'Djibouti', 'DM'=>'Dominica', 'DO'=>'Dominican Republic', 'TP'=>'East Timor', 'XE'=>'East Timor', 'EC'=>'Ecuador', 'EG'=>'Egypt', 'SV'=>'El Salvador', 'GQ'=>'Equatorial Guinea', 'ER'=>'Eritrea', 'EE'=>'Estonia', 'ET'=>'Ethiopia', 'FK'=>'Falkland Islands (Malvinas)', 'FO'=>'Faroe Islands', 'FJ'=>'Fiji', 'FI'=>'Finland', 'FR'=>'France', 'FX'=>'France, Metropolitan', 'GF'=>'French Guiana', 'PF'=>'French Polynesia', 'TF'=>'French Southern Territories', 'GA'=>'Gabon', 'GM'=>'Gambia', 'GE'=>'Georgia', 'DE'=>'Germany', 'GH'=>'Ghana', 'GI'=>'Gibraltar', 'GR'=>'Greece', 'GL'=>'Greenland', 'GD'=>'Grenada', 'GP'=>'Guadeloupe', 'GU'=>'Guam', 'GT'=>'Guatemala', 'GN'=>'Guinea', 'GW'=>'Guinea-bissau', 'GY'=>'Guyana', 'HT'=>'Haiti', 'HM'=>'Heard and Mc Donald Islands', 'HN'=>'Honduras', 'HK'=>'Hong Kong', 'HU'=>'Hungary', 'IS'=>'Iceland', 'IN'=>'India', 'ID'=>'Indonesia', 'IR'=>'Iran (Islamic Republic of)', 'IQ'=>'Iraq', 'IE'=>'Ireland', 'IL'=>'Israel', 'IT'=>'Italy', 'JM'=>'Jamaica', 'JP'=>'Japan', 'XJ'=>'Jersey', 'JO'=>'Jordan', 'KZ'=>'Kazakhstan', 'KE'=>'Kenya', 'KI'=>'Kiribati', 'KP'=>'Korea, Democratic People\'s Republic of', 'KR'=>'Korea, Republic of', 'KW'=>'Kuwait', 'KG'=>'Kyrgyzstan', 'LA'=>'Lao People\'s Democratic Republic', 'LV'=>'Latvia', 'LB'=>'Lebanon', 'LS'=>'Lesotho', 'LR'=>'Liberia', 'LY'=>'Libyan Arab Jamahiriya', 'LI'=>'Liechtenstein', 'LT'=>'Lithuania', 'LU'=>'Luxembourg', 'MO'=>'Macau', 'MK'=>'Macedonia, The Former Yugoslav Republic of', 'MG'=>'Madagascar', 'MW'=>'Malawi', 'MY'=>'Malaysia', 'MV'=>'Maldives', 'ML'=>'Mali', 'MT'=>'Malta', 'MH'=>'Marshall Islands', 'MQ'=>'Martinique', 'MR'=>'Mauritania', 'MU'=>'Mauritius', 'YT'=>'Mayotte', 'MX'=>'Mexico', 'FM'=>'Micronesia, Federated States of', 'MD'=>'Moldova, Republic of', 'MC'=>'Monaco', 'MN'=>'Mongolia', 'ME'=>'Montenegro', 'MS'=>'Montserrat', 'MA'=>'Morocco', 'MZ'=>'Mozambique', 'MM'=>'Myanmar', 'NA'=>'Namibia', 'NR'=>'Nauru', 'NP'=>'Nepal', 'NL'=>'Netherlands', 'AN'=>'Netherlands Antilles', 'NC'=>'New Caledonia', 'NZ'=>'New Zealand', 'NI'=>'Nicaragua', 'NE'=>'Niger', 'NG'=>'Nigeria', 'NU'=>'Niue', 'NF'=>'Norfolk Island', 'MP'=>'Northern Mariana Islands', 'NO'=>'Norway', 'OM'=>'Oman', 'PK'=>'Pakistan', 'PW'=>'Palau', 'PA'=>'Panama', 'PG'=>'Papua New Guinea', 'PY'=>'Paraguay', 'PE'=>'Peru', 'PH'=>'Philippines', 'PN'=>'Pitcairn', 'PL'=>'Poland', 'PT'=>'Portugal', 'PR'=>'Puerto Rico', 'QA'=>'Qatar', 'RE'=>'Reunion', 'RO'=>'Romania', 'RU'=>'Russian Federation', 'RW'=>'Rwanda', 'KN'=>'Saint Kitts and Nevis', 'LC'=>'Saint Lucia', 'VC'=>'Saint Vincent and the Grenadines', 'WS'=>'Samoa', 'SM'=>'San Marino', 'ST'=>'Sao Tome and Principe', 'SA'=>'Saudi Arabia', 'SN'=>'Senegal', 'RS'=>'Serbia', 'SC'=>'Seychelles', 'SL'=>'Sierra Leone', 'SG'=>'Singapore', 'SK'=>'Slovakia (Slovak Republic)', 'SI'=>'Slovenia', 'SB'=>'Solomon Islands', 'SO'=>'Somalia', 'ZA'=>'South Africa', 'GS'=>'South Georgia and the South Sandwich Islands', 'ES'=>'Spain', 'LK'=>'Sri Lanka', 'XB'=>'St. Barthelemy', 'XU'=>'St. Eustatius', 'SH'=>'St. Helena', 'PM'=>'St. Pierre and Miquelon', 'SD'=>'Sudan', 'SR'=>'Suriname', 'SJ'=>'Svalbard and Jan Mayen Islands', 'SZ'=>'Swaziland', 'SE'=>'Sweden', 'CH'=>'Switzerland', 'SY'=>'Syrian Arab Republic', 'TW'=>'Taiwan', 'TJ'=>'Tajikistan', 'TZ'=>'Tanzania, United Republic of', 'TH'=>'Thailand', 'DC'=>'The Democratic Republic of Congo', 'TG'=>'Togo', 'TK'=>'Tokelau', 'TO'=>'Tonga', 'TT'=>'Trinidad and Tobago', 'TN'=>'Tunisia', 'TR'=>'Turkey', 'TM'=>'Turkmenistan', 'TC'=>'Turks and Caicos Islands', 'TV'=>'Tuvalu', 'UG'=>'Uganda', 'UA'=>'Ukraine', 'AE'=>'United Arab Emirates', 'GB'=>'United Kingdom', 'US'=>'United States', 'UM'=>'United States Minor Outlying Islands', 'UY'=>'Uruguay', 'UZ'=>'Uzbekistan', 'VU'=>'Vanuatu', 'VA'=>'Vatican City State (Holy See)', 'VE'=>'Venezuela', 'VN'=>'Viet Nam', 'VG'=>'Virgin Islands (British)', 'VI'=>'Virgin Islands (U.S.)', 'WF'=>'Wallis and Futuna Islands', 'EH'=>'Western Sahara', 'YE'=>'Yemen', 'ZM'=>'Zambia', 'ZW'=>'Zimbabwe');
    $tpl = JPATH_BASE.DS."modules".DS."mod_paymybill".DS."mailtpl".DS.$type.".html";
    $fh = fopen($tpl, 'r');
    $body = fread($fh, filesize($tpl));
    fclose($fh);
    $status="$type";
    if($type=='pending')
    {
      $status='<strong><font color="#2554C7">'.ucfirst($type).'</font></strong>';
    }
    if($type=='success')
    {
      $status       = '<strong><font color="#4CC417">'.ucfirst($type).'</font></strong>';
    }
    if($type=='cancelled')
    {
      $status='<strong><font color="#DC381F">'.ucfirst($type).'</font></strong>';
    }
    $body=str_ireplace("{trans_id}",$details['order_id'],$body);
    $body=str_ireplace("{status}",$status,$body);
    $body=str_ireplace("{product}",$params['product_name'],$body);
    if($params['show_inv_field']==1)
    {
      $body=str_ireplace("{invoice_text}",$params['inv_text'],$body);
      $body=str_ireplace("{invoice}",$details['InvNr'],$body);
    }
    else
    {
      $body=str_ireplace("{invoice_text}",$params['inv_text'],$body);
      $body=str_ireplace("{invoice}",'N/A',$body);
    }
    $body = str_ireplace("{price}",$details['payment']." ".$details['currency'],$body);
    $body = str_ireplace("{fname}",$details['BillFname'],$body);
    $body = str_ireplace("{lname}",$details['BillLname'],$body);
    $body = str_ireplace("{adress}",$details['BillAddr'],$body);
    $body = str_ireplace("{city}",$details['BillCity'],$body);
    $body = str_ireplace("{zip}",$details['BillZip'],$body);
    $body = str_ireplace("{state}",$details['BillState'],$body);
    $body = str_ireplace("{country}",$country_codes_arr[$details['BillCountry']],$body);
    $body = str_ireplace("{email}",$details['BillEmail'],$body);
    $body = str_ireplace("{phone}",$details['BillPhone'],$body);
    $body = str_ireplace("{method}",$payment_method,$body);
    $body = str_ireplace("{mode}",$details['mode'],$body);
    $body = str_ireplace("{sitelink}","http://".$_SERVER['HTTP_HOST'],$body);
    
    $mailer = JFactory::getMailer();
    $config = JFactory::getConfig();
    $sender = array( 
    $config->get( 'mailfrom' ),
    $config->get( 'fromname' ) );
    $mailer->setSender($sender);
    $send_to=array();

    //get where to send mails
    if($type=='pending' && $params['mail_pending']==1)
    {
      $send_to[]=$params['confirmationEmail'];
    }
    if($type=='pending' && $params['mail_pending']==2)
    {
      $send_to[]=$details['BillEmail'];
    }
    if($type=='pending' && $params['mail_pending']==3)
    {
      $send_to[]=$params['confirmationEmail'];
      $send_to[]=$details['BillEmail'];
    }
    
    if($type=='success' && $params['mail_success']==1)
    {
      $send_to[]=$params['confirmationEmail'];
    }
    if($type=='success' && $params['mail_success']==2)
    {
      $send_to[]=$details['BillEmail'];
    }
    if($type=='success' && $params['mail_success']==3)
    {
      $send_to[]=$params['confirmationEmail'];
      $send_to[]=$details['BillEmail'];
    }
    
    if($type=='cancelled' && $params['mail_cancelled']==1)
    {
      $send_to[]=$params['confirmationEmail'];
    }
    if($type=='cancelled' && $params['mail_cancelled']==2)
    {
      $send_to[]=$details['BillEmail'];
    }
    if($type=='cancelled' && $params['mail_cancelled']==3)
    {
      $send_to[]=$params['confirmationEmail'];
      $send_to[]=$details['BillEmail'];
    }
    //add attachments
    $attachments=array();
    $attachments2=array();
    if($type=='success')
    {
      $attach=$params['attach'];
      $attach2=$params['attach2'];
      if($attach!=-1 && $attach!="")
      {
        if(is_file(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."documents".DS.$attach))
        {
          $attachments[]=JPATH_BASE.DS."modules".DS."mod_paymybill".DS."documents".DS.$attach;
          $attachments2[]=$attach;
        }
      }
      if($attach2!="")
      {
        $tarr=explode(",",$attach2);
        foreach($tarr as $t)
        {
          if(is_file(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."documents".DS.trim(trim($t,DS))))
          {
            $attachments[]=JPATH_BASE.DS."modules".DS."mod_paymybill".DS."documents".DS.trim(trim($t,DS));
            $attachments2[]=trim(trim($t,DS));
          }
        }
      }
    }
    
    $attachments=array_unique($attachments);
    $attachments2=array_unique($attachments2);
    if(count($attachments)>0)
    {
      $mailer->addAttachment($attachments);
      $attachments_str=implode(",",$attachments2);
      $attachments_str='<p>'.JText::_( 'PMB_MODULE_EMAIL_ATTACHMENTS' ).$attachments_str.'</p>';
      $body=str_ireplace("{attachments}",$attachments_str,$body);
    }
    else
    {
      $body=str_ireplace("{attachments}","",$body);
    }
    if($params['license_key']==1)
    {
      $license_code=$details['license'];
      $license_code='<p>'.JText::_( 'PMB_MODULE_EMAIL_LICENSE_KEY' ).'<strong>'.$license_code.'</strong></p>';
      $body=str_ireplace("{license}",$license_code,$body);
    }
    else
    {
      $body=str_ireplace("{license}","",$body);
    }
    $mailer->addRecipient($send_to);
    $mailer->setSubject('A payment for the amount: ' . $details['payment'] . ' and reference: '. $details['InvNr'] . ' has been made');
    $mailer->isHTML(true);
    $mailer->setBody($body);
    $mailer->Send();
  }
  function getOrderDetails($order_id)
  {
    $order=array();
    $db=JFactory::getDBO();
    $query="select * from #__paymybill where order_id='".$db->escape($order_id)."'";
    $db->setQuery($query);
    $opt=$db->loadObject();
    if(isset($opt->order_id))
    {
      foreach($opt as $key => $val)
      {
        if(!isset($order[$key]))
          $order[$key]='';
        $order[$key]=$val;
      }
    }
    return $order;
  }
  function saveOrder($payment,$mode,$params,$details,$payment_method)
  {
    //save order in db
    $payment_type=($params['payment_type']==0)?"Fixed":"Custom Input";
    $trans_cost=$params[strtolower($payment_method).'_trans_cost']+0;
    $trans_percent_cost=$params[strtolower($payment_method).'_trans_percent_cost']+0;
    $payment_title=$params[strtolower($payment_method).'_title'];
    //add attachments
    $attachments=array();
    $attach=$params['attach'];
    $attach2=$params['attach2'];
    
    if($attach!=-1 && $attach!="")
    {
      if(is_file(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."documents".DS.$attach))
      {
        $attachments[]=$attach;
      }
    }
    if($attach2!="")
    {
      $tarr=explode(",",$attach2);
      foreach($tarr as $t)
      {
        if(is_file(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."documents".DS.trim(trim($t,DS))))
        {
          $attachments[]=trim(trim($t,DS));
        }
      }
    }
    $attachments=array_unique($attachments);
    $attachments_str='';
    if(count($attachments)>0)
    {
      $attachments_str=implode(",",$attachments);
    }
    //get license key
    $license_code='';
    if($params['license_key']==1)
    {
      $license_code=$this->getNewLicenseKey();
    }

    // TODO refactor to modern Joomla! Database insert
    $dateTimeTransaction    = date('Y-m-d H:i:s');
    $db=JFactory::getDBO();
    $query="
    insert into #__paymybill set
    `order_id`='".$db->escape($details['order_id'])."',
    `attachments`='".$db->escape($attachments_str)."',
    `product_name`='".$db->escape($params['product_name'])."',
    `payment_title`='".$db->escape($payment_title)."',
    `currency`='".$db->escape($details['currency'])."',
    `payment_method`='".$db->escape($payment_method)."',
    `subtotal`='".$db->escape($details['subtotal'])."',
    `trans_cost`='".$db->escape($trans_cost)."',
    `trans_percent_cost`='".$db->escape($trans_percent_cost)."',
    `payment_type`='".$db->escape($payment_type)."',
    `mode`='".$db->escape($mode)."',
    `status`='pending',
    `payment`='".$db->escape($payment)."',
    `BillFname`='".$db->escape($details['BillFname'])."',
    `BillLname`='".$db->escape($details['BillLname'])."',
    `BillAddr`='".$db->escape($details['BillAddr'])."',
    `BillCity`='".$db->escape($details['BillCity'])."',
    `BillZip`='".$db->escape($details['BillZip'])."',
    `BillState`='".$db->escape($details['BillState'])."',
    `BillCountry`='".$db->escape($details['BillCountry'])."',
    `BillEmail`='".$db->escape($details['BillEmail'])."',
    `BillPhone`='".$db->escape($details['BillPhone'])."',
    `InvNr`='".$db->escape($details['InvNr'])."',
    `license`='".$db->escape($license_code)."',
    `cdate`='".$dateTimeTransaction."',
    `mdate`='".$dateTimeTransaction."'
    ";
    $db->setQuery($query);
    $db->query();
  }
  function createRandomKey($amount)
  {
  	$keyset  = "abcdefghijklmABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  	$randkey = "";
  	for ($i=0; $i<$amount; $i++)
    {
  		$randkey .= substr($keyset, rand(0, strlen($keyset)-1), 1);
    }
  	return $randkey;	
  }
  function getNewLicenseKey()
  {
    $key=$this->createRandomKey(15);
    $db=&JFactory::getDBO();
    $query="select license from #__paymybill where license='".$db->escape($key)."'";
    $db->setQuery($query);
    $obj=$db->loadObject();
    if(isset($obj->license))
    {
      return $this->getNewLicenseKey();
    }
    return $key;
  }
  function setOrderCancelled($params,$details,$payment_method)
  {
    $db=JFactory::getDBO();
    $query="
    update #__paymybill set
    `status`='cancelled',
    `mdate`='".time()."'
    where `order_id`='".$db->escape($details['order_id'])."'
    ";
    $db->setQuery($query);
    $db->query();
  }
  function setOrderSuccess($params,$details,$payment_method)
  {
    $db=JFactory::getDBO();
    $query="
    update #__paymybill set
    `status`='success',
    `mdate`='".time()."'
    where `order_id`='".$db->escape($details['order_id'])."'
    ";
    $db->setQuery($query);
    $db->query();
  }
  function sendMailtoAdmin($msg,$subj)
  {
    $mailer = JFactory::getMailer();
    $config = JFactory::getConfig();
    $sender = array( 
        $config->get( 'mailfrom' ),
        $config->get( 'fromname' ) );
     
    $mailer->setSender($sender);
    
    $mailer->addRecipient($config->get( 'mailfrom' ));
    $body   = $msg;
    $mailer->setSubject(juri::base().$subj);
    $mailer->isHTML(true);
    $body=nl2br($body);
    $mailer->setBody($body);
    $send = $mailer->Send();
  }
}
?>