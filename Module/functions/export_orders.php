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
$dirname=str_ireplace(DS.'modules'.DS.'mod_paymybill'.DS.'functions','', dirname(__FILE__));

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

$access=0;
$user=JFactory::getUser();
if(!$user->get('isRoot'))
{
  die('Access denied');
}
$query="select * from #__paymybill where 1=1 order by mdate desc";
$db=JFactory::getDBO();
$db->setQuery($query);
$items=$db->loadObjectList();
$country_codes_arr=array('AF'=>'Afghanistan', 'AL'=>'Albania', 'DZ'=>'Algeria', 'AS'=>'American Samoa', 'AD'=>'Andorra', 'AO'=>'Angola', 'AI'=>'Anguilla', 'AQ'=>'Antarctica', 'AG'=>'Antigua and Barbuda', 'AR'=>'Argentina', 'AM'=>'Armenia', 'AW'=>'Aruba', 'AU'=>'Australia', 'AT'=>'Austria', 'AZ'=>'Azerbaijan', 'BS'=>'Bahamas', 'BH'=>'Bahrain', 'BD'=>'Bangladesh', 'BB'=>'Barbados', 'BY'=>'Belarus', 'BE'=>'Belgium', 'BZ'=>'Belize', 'BJ'=>'Benin', 'BM'=>'Bermuda', 'BT'=>'Bhutan', 'BO'=>'Bolivia', 'BA'=>'Bosnia and Herzegowina', 'BW'=>'Botswana', 'BV'=>'Bouvet Island', 'BR'=>'Brazil', 'IO'=>'British Indian Ocean Territory', 'BN'=>'Brunei Darussalam', 'BG'=>'Bulgaria', 'BF'=>'Burkina Faso', 'BI'=>'Burundi', 'KH'=>'Cambodia', 'CM'=>'Cameroon', 'CA'=>'Canada', 'XC'=>'Canary Islands', 'CV'=>'Cape Verde', 'KY'=>'Cayman Islands', 'CF'=>'Central African Republic', 'TD'=>'Chad', 'CL'=>'Chile', 'CN'=>'China', 'CX'=>'Christmas Island', 'CC'=>'Cocos (Keeling) Islands', 'CO'=>'Colombia', 'KM'=>'Comoros', 'CG'=>'Congo', 'CK'=>'Cook Islands', 'CR'=>'Costa Rica', 'CI'=>'Cote D\'Ivoire', 'HR'=>'Croatia', 'CU'=>'Cuba', 'CY'=>'Cyprus', 'CZ'=>'Czech Republic', 'DK'=>'Denmark', 'DJ'=>'Djibouti', 'DM'=>'Dominica', 'DO'=>'Dominican Republic', 'TP'=>'East Timor', 'XE'=>'East Timor', 'EC'=>'Ecuador', 'EG'=>'Egypt', 'SV'=>'El Salvador', 'GQ'=>'Equatorial Guinea', 'ER'=>'Eritrea', 'EE'=>'Estonia', 'ET'=>'Ethiopia', 'FK'=>'Falkland Islands (Malvinas)', 'FO'=>'Faroe Islands', 'FJ'=>'Fiji', 'FI'=>'Finland', 'FR'=>'France', 'FX'=>'France, Metropolitan', 'GF'=>'French Guiana', 'PF'=>'French Polynesia', 'TF'=>'French Southern Territories', 'GA'=>'Gabon', 'GM'=>'Gambia', 'GE'=>'Georgia', 'DE'=>'Germany', 'GH'=>'Ghana', 'GI'=>'Gibraltar', 'GR'=>'Greece', 'GL'=>'Greenland', 'GD'=>'Grenada', 'GP'=>'Guadeloupe', 'GU'=>'Guam', 'GT'=>'Guatemala', 'GN'=>'Guinea', 'GW'=>'Guinea-bissau', 'GY'=>'Guyana', 'HT'=>'Haiti', 'HM'=>'Heard and Mc Donald Islands', 'HN'=>'Honduras', 'HK'=>'Hong Kong', 'HU'=>'Hungary', 'IS'=>'Iceland', 'IN'=>'India', 'ID'=>'Indonesia', 'IR'=>'Iran (Islamic Republic of)', 'IQ'=>'Iraq', 'IE'=>'Ireland', 'IL'=>'Israel', 'IT'=>'Italy', 'JM'=>'Jamaica', 'JP'=>'Japan', 'XJ'=>'Jersey', 'JO'=>'Jordan', 'KZ'=>'Kazakhstan', 'KE'=>'Kenya', 'KI'=>'Kiribati', 'KP'=>'Korea, Democratic People\'s Republic of', 'KR'=>'Korea, Republic of', 'KW'=>'Kuwait', 'KG'=>'Kyrgyzstan', 'LA'=>'Lao People\'s Democratic Republic', 'LV'=>'Latvia', 'LB'=>'Lebanon', 'LS'=>'Lesotho', 'LR'=>'Liberia', 'LY'=>'Libyan Arab Jamahiriya', 'LI'=>'Liechtenstein', 'LT'=>'Lithuania', 'LU'=>'Luxembourg', 'MO'=>'Macau', 'MK'=>'Macedonia, The Former Yugoslav Republic of', 'MG'=>'Madagascar', 'MW'=>'Malawi', 'MY'=>'Malaysia', 'MV'=>'Maldives', 'ML'=>'Mali', 'MT'=>'Malta', 'MH'=>'Marshall Islands', 'MQ'=>'Martinique', 'MR'=>'Mauritania', 'MU'=>'Mauritius', 'YT'=>'Mayotte', 'MX'=>'Mexico', 'FM'=>'Micronesia, Federated States of', 'MD'=>'Moldova, Republic of', 'MC'=>'Monaco', 'MN'=>'Mongolia', 'ME'=>'Montenegro', 'MS'=>'Montserrat', 'MA'=>'Morocco', 'MZ'=>'Mozambique', 'MM'=>'Myanmar', 'NA'=>'Namibia', 'NR'=>'Nauru', 'NP'=>'Nepal', 'NL'=>'Netherlands', 'AN'=>'Netherlands Antilles', 'NC'=>'New Caledonia', 'NZ'=>'New Zealand', 'NI'=>'Nicaragua', 'NE'=>'Niger', 'NG'=>'Nigeria', 'NU'=>'Niue', 'NF'=>'Norfolk Island', 'MP'=>'Northern Mariana Islands', 'NO'=>'Norway', 'OM'=>'Oman', 'PK'=>'Pakistan', 'PW'=>'Palau', 'PA'=>'Panama', 'PG'=>'Papua New Guinea', 'PY'=>'Paraguay', 'PE'=>'Peru', 'PH'=>'Philippines', 'PN'=>'Pitcairn', 'PL'=>'Poland', 'PT'=>'Portugal', 'PR'=>'Puerto Rico', 'QA'=>'Qatar', 'RE'=>'Reunion', 'RO'=>'Romania', 'RU'=>'Russian Federation', 'RW'=>'Rwanda', 'KN'=>'Saint Kitts and Nevis', 'LC'=>'Saint Lucia', 'VC'=>'Saint Vincent and the Grenadines', 'WS'=>'Samoa', 'SM'=>'San Marino', 'ST'=>'Sao Tome and Principe', 'SA'=>'Saudi Arabia', 'SN'=>'Senegal', 'RS'=>'Serbia', 'SC'=>'Seychelles', 'SL'=>'Sierra Leone', 'SG'=>'Singapore', 'SK'=>'Slovakia (Slovak Republic)', 'SI'=>'Slovenia', 'SB'=>'Solomon Islands', 'SO'=>'Somalia', 'ZA'=>'South Africa', 'GS'=>'South Georgia and the South Sandwich Islands', 'ES'=>'Spain', 'LK'=>'Sri Lanka', 'XB'=>'St. Barthelemy', 'XU'=>'St. Eustatius', 'SH'=>'St. Helena', 'PM'=>'St. Pierre and Miquelon', 'SD'=>'Sudan', 'SR'=>'Suriname', 'SJ'=>'Svalbard and Jan Mayen Islands', 'SZ'=>'Swaziland', 'SE'=>'Sweden', 'CH'=>'Switzerland', 'SY'=>'Syrian Arab Republic', 'TW'=>'Taiwan', 'TJ'=>'Tajikistan', 'TZ'=>'Tanzania, United Republic of', 'TH'=>'Thailand', 'DC'=>'The Democratic Republic of Congo', 'TG'=>'Togo', 'TK'=>'Tokelau', 'TO'=>'Tonga', 'TT'=>'Trinidad and Tobago', 'TN'=>'Tunisia', 'TR'=>'Turkey', 'TM'=>'Turkmenistan', 'TC'=>'Turks and Caicos Islands', 'TV'=>'Tuvalu', 'UG'=>'Uganda', 'UA'=>'Ukraine', 'AE'=>'United Arab Emirates', 'GB'=>'United Kingdom', 'US'=>'United States', 'UM'=>'United States Minor Outlying Islands', 'UY'=>'Uruguay', 'UZ'=>'Uzbekistan', 'VU'=>'Vanuatu', 'VA'=>'Vatican City State (Holy See)', 'VE'=>'Venezuela', 'VN'=>'Viet Nam', 'VG'=>'Virgin Islands (British)', 'VI'=>'Virgin Islands (U.S.)', 'WF'=>'Wallis and Futuna Islands', 'EH'=>'Western Sahara', 'YE'=>'Yemen', 'ZM'=>'Zambia', 'ZW'=>'Zimbabwe');
if(count($items)>0)
{
  $items1=array();
  foreach($items as $item)
  {
    $item1=new stdClass;
    foreach($item as $key => $val)
    {
      if($key=='cdate' || $key=='mdate')
      {
        $item1->$key=date("Y-m-d H:i:s",$val);
      }
      else if($key=='BillCountry')
      {
        $item1->$key=$country_codes_arr[$val];
      }
      else
      {
        $item1->$key=$val;
      }
    }
    $items1[]=$item1;
  }
  $items=$items1;
  $export=new CSVExport();
  //print_r($items);exit;
  $header=array(
  'Order ID',
  'Product Name',
  'Payment Title',
  'Payment Method',
  'Mode',
  'Currency',
  'Status',
  'Payment Total',
  'Subtotal',
  'Fee on Transaction',
  'Percent fee on transaction',
  'Payment Type',
  'First name',
  'Last name',
  'Address',
  'City',
  'Postcode',
  'State',
  'Country',
  'Email',
  'Phone',
  'InvNr',
  'License',
  'Attachments',
  'Creation date',
  'Modification date'
  );
  $keys=array(
  'order_id',
  'product_name',
  'payment_title',
  'payment_method',
  'mode',
  'currency',
  'status',
  'payment',
  'subtotal',
  'trans_cost',
  'trans_percent_cost',
  'payment_type',
  'BillFname',
  'BillLname',
  'BillAddr',
  'BillCity',
  'BillZip',
  'BillState',
  'BillCountry',
  'BillEmail',
  'BillPhone',
  'InvNr',
  'license',
  'attachments',
  'cdate',
  'mdate'
  );
  if(count($header)!=count($keys))
  {
  die("Nr of headers different from values.");
  }
  $export->exportCSV($items,$keys,$header);
}
else
{
  echo 'No records';
}
class CSVExport{
	public function __construct(){
	}
	public function exportCSV($result,$keys,$header)
	{
		$fname = 'orders_'.date("d_m_Y").'.csv';
    $separator=",";
		$fp = fopen($fname, 'w');
    fputcsv($fp, $header,$separator,'"');
		if ($fp && $result) {
   		 header('Content-Type: application/csv');
   		 header('Content-Disposition: inline; filename='.$fname);
        foreach($result as $res)
        {
          $temp=array();
          foreach($keys as $key)
          {
            if(isset($res->$key))
            {
              $temp[]=$res->$key;
            }
            else
            {
              $temp[]="";
            }
          }
          if(count($temp)>0)
            fputcsv($fp, $temp,$separator,'"');
        }
			readfile($fname);
			fclose($fp);
		}
 	unlink($fname);
	die;
	}
}
?>