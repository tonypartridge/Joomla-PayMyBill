<?php
/**
 *
 * @package	    Pay My Bill Module
 * @subpackage	Pay My Bill Module
 * @version     1.0.0
 * @description Pay My Bill Module
 * @copyright	  Copyright © 2013 - All rights reserved.
 * @license		  GNU General Public License v2.0
 * @author		  SoftPill.Eu
 * @author mail	mail@softpill.eu
 * @website		  www.softpill.eu
 *
 */
//ini_set("display_errors",0);
//error_reporting(E_ALL);
if(!defined('_JEXEC'))
{
  define('_JEXEC', 1);
}
defined( '_JEXEC' ) or die( 'Restricted access' );
header("Content-type: text/javascript");

if (version_compare(PHP_VERSION, '5.3.1', '<'))
{
	die('Your host needs to use PHP 5.3.1 or higher to run this version of Joomla!');
}
define('DS', DIRECTORY_SEPARATOR);
$dirname=str_ireplace(DS.'modules'.DS.'mod_paymybill'.DS.'js','', dirname(__FILE__));

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
$app = JFactory::getApplication('site');

$lang = JFactory::getLanguage();
$extension = 'mod_paymybill';
$language_tag = $lang->getTag();
$reload = true;
$lang->load($extension, $dirname, $language_tag, $reload);
$country_codes_arr=array('AF'=>'Afghanistan', 'AL'=>'Albania', 'DZ'=>'Algeria', 'AS'=>'American Samoa', 'AD'=>'Andorra', 'AO'=>'Angola', 'AI'=>'Anguilla', 'AQ'=>'Antarctica', 'AG'=>'Antigua and Barbuda', 'AR'=>'Argentina', 'AM'=>'Armenia', 'AW'=>'Aruba', 'AU'=>'Australia', 'AT'=>'Austria', 'AZ'=>'Azerbaijan', 'BS'=>'Bahamas', 'BH'=>'Bahrain', 'BD'=>'Bangladesh', 'BB'=>'Barbados', 'BY'=>'Belarus', 'BE'=>'Belgium', 'BZ'=>'Belize', 'BJ'=>'Benin', 'BM'=>'Bermuda', 'BT'=>'Bhutan', 'BO'=>'Bolivia', 'BA'=>'Bosnia and Herzegowina', 'BW'=>'Botswana', 'BV'=>'Bouvet Island', 'BR'=>'Brazil', 'IO'=>'British Indian Ocean Territory', 'BN'=>'Brunei Darussalam', 'BG'=>'Bulgaria', 'BF'=>'Burkina Faso', 'BI'=>'Burundi', 'KH'=>'Cambodia', 'CM'=>'Cameroon', 'CA'=>'Canada', 'XC'=>'Canary Islands', 'CV'=>'Cape Verde', 'KY'=>'Cayman Islands', 'CF'=>'Central African Republic', 'TD'=>'Chad', 'CL'=>'Chile', 'CN'=>'China', 'CX'=>'Christmas Island', 'CC'=>'Cocos (Keeling) Islands', 'CO'=>'Colombia', 'KM'=>'Comoros', 'CG'=>'Congo', 'CK'=>'Cook Islands', 'CR'=>'Costa Rica', 'CI'=>'Cote D\'Ivoire', 'HR'=>'Croatia', 'CU'=>'Cuba', 'CY'=>'Cyprus', 'CZ'=>'Czech Republic', 'DK'=>'Denmark', 'DJ'=>'Djibouti', 'DM'=>'Dominica', 'DO'=>'Dominican Republic', 'TP'=>'East Timor', 'XE'=>'East Timor', 'EC'=>'Ecuador', 'EG'=>'Egypt', 'SV'=>'El Salvador', 'GQ'=>'Equatorial Guinea', 'ER'=>'Eritrea', 'EE'=>'Estonia', 'ET'=>'Ethiopia', 'FK'=>'Falkland Islands (Malvinas)', 'FO'=>'Faroe Islands', 'FJ'=>'Fiji', 'FI'=>'Finland', 'FR'=>'France', 'FX'=>'France, Metropolitan', 'GF'=>'French Guiana', 'PF'=>'French Polynesia', 'TF'=>'French Southern Territories', 'GA'=>'Gabon', 'GM'=>'Gambia', 'GE'=>'Georgia', 'DE'=>'Germany', 'GH'=>'Ghana', 'GI'=>'Gibraltar', 'GR'=>'Greece', 'GL'=>'Greenland', 'GD'=>'Grenada', 'GP'=>'Guadeloupe', 'GU'=>'Guam', 'GT'=>'Guatemala', 'GN'=>'Guinea', 'GW'=>'Guinea-bissau', 'GY'=>'Guyana', 'HT'=>'Haiti', 'HM'=>'Heard and Mc Donald Islands', 'HN'=>'Honduras', 'HK'=>'Hong Kong', 'HU'=>'Hungary', 'IS'=>'Iceland', 'IN'=>'India', 'ID'=>'Indonesia', 'IR'=>'Iran (Islamic Republic of)', 'IQ'=>'Iraq', 'IE'=>'Ireland', 'IL'=>'Israel', 'IT'=>'Italy', 'JM'=>'Jamaica', 'JP'=>'Japan', 'XJ'=>'Jersey', 'JO'=>'Jordan', 'KZ'=>'Kazakhstan', 'KE'=>'Kenya', 'KI'=>'Kiribati', 'KP'=>'Korea, Democratic People\'s Republic of', 'KR'=>'Korea, Republic of', 'KW'=>'Kuwait', 'KG'=>'Kyrgyzstan', 'LA'=>'Lao People\'s Democratic Republic', 'LV'=>'Latvia', 'LB'=>'Lebanon', 'LS'=>'Lesotho', 'LR'=>'Liberia', 'LY'=>'Libyan Arab Jamahiriya', 'LI'=>'Liechtenstein', 'LT'=>'Lithuania', 'LU'=>'Luxembourg', 'MO'=>'Macau', 'MK'=>'Macedonia, The Former Yugoslav Republic of', 'MG'=>'Madagascar', 'MW'=>'Malawi', 'MY'=>'Malaysia', 'MV'=>'Maldives', 'ML'=>'Mali', 'MT'=>'Malta', 'MH'=>'Marshall Islands', 'MQ'=>'Martinique', 'MR'=>'Mauritania', 'MU'=>'Mauritius', 'YT'=>'Mayotte', 'MX'=>'Mexico', 'FM'=>'Micronesia, Federated States of', 'MD'=>'Moldova, Republic of', 'MC'=>'Monaco', 'MN'=>'Mongolia', 'ME'=>'Montenegro', 'MS'=>'Montserrat', 'MA'=>'Morocco', 'MZ'=>'Mozambique', 'MM'=>'Myanmar', 'NA'=>'Namibia', 'NR'=>'Nauru', 'NP'=>'Nepal', 'NL'=>'Netherlands', 'AN'=>'Netherlands Antilles', 'NC'=>'New Caledonia', 'NZ'=>'New Zealand', 'NI'=>'Nicaragua', 'NE'=>'Niger', 'NG'=>'Nigeria', 'NU'=>'Niue', 'NF'=>'Norfolk Island', 'MP'=>'Northern Mariana Islands', 'NO'=>'Norway', 'OM'=>'Oman', 'PK'=>'Pakistan', 'PW'=>'Palau', 'PA'=>'Panama', 'PG'=>'Papua New Guinea', 'PY'=>'Paraguay', 'PE'=>'Peru', 'PH'=>'Philippines', 'PN'=>'Pitcairn', 'PL'=>'Poland', 'PT'=>'Portugal', 'PR'=>'Puerto Rico', 'QA'=>'Qatar', 'RE'=>'Reunion', 'RO'=>'Romania', 'RU'=>'Russian Federation', 'RW'=>'Rwanda', 'KN'=>'Saint Kitts and Nevis', 'LC'=>'Saint Lucia', 'VC'=>'Saint Vincent and the Grenadines', 'WS'=>'Samoa', 'SM'=>'San Marino', 'ST'=>'Sao Tome and Principe', 'SA'=>'Saudi Arabia', 'SN'=>'Senegal', 'RS'=>'Serbia', 'SC'=>'Seychelles', 'SL'=>'Sierra Leone', 'SG'=>'Singapore', 'SK'=>'Slovakia (Slovak Republic)', 'SI'=>'Slovenia', 'SB'=>'Solomon Islands', 'SO'=>'Somalia', 'ZA'=>'South Africa', 'GS'=>'South Georgia and the South Sandwich Islands', 'ES'=>'Spain', 'LK'=>'Sri Lanka', 'XB'=>'St. Barthelemy', 'XU'=>'St. Eustatius', 'SH'=>'St. Helena', 'PM'=>'St. Pierre and Miquelon', 'SD'=>'Sudan', 'SR'=>'Suriname', 'SJ'=>'Svalbard and Jan Mayen Islands', 'SZ'=>'Swaziland', 'SE'=>'Sweden', 'CH'=>'Switzerland', 'SY'=>'Syrian Arab Republic', 'TW'=>'Taiwan', 'TJ'=>'Tajikistan', 'TZ'=>'Tanzania, United Republic of', 'TH'=>'Thailand', 'DC'=>'The Democratic Republic of Congo', 'TG'=>'Togo', 'TK'=>'Tokelau', 'TO'=>'Tonga', 'TT'=>'Trinidad and Tobago', 'TN'=>'Tunisia', 'TR'=>'Turkey', 'TM'=>'Turkmenistan', 'TC'=>'Turks and Caicos Islands', 'TV'=>'Tuvalu', 'UG'=>'Uganda', 'UA'=>'Ukraine', 'AE'=>'United Arab Emirates', 'GB'=>'United Kingdom', 'US'=>'United States', 'UM'=>'United States Minor Outlying Islands', 'UY'=>'Uruguay', 'UZ'=>'Uzbekistan', 'VU'=>'Vanuatu', 'VA'=>'Vatican City State (Holy See)', 'VE'=>'Venezuela', 'VN'=>'Viet Nam', 'VG'=>'Virgin Islands (British)', 'VI'=>'Virgin Islands (U.S.)', 'WF'=>'Wallis and Futuna Islands', 'EH'=>'Western Sahara', 'YE'=>'Yemen', 'ZM'=>'Zambia', 'ZW'=>'Zimbabwe');
?>
var pmbCountries=<?php echo json_encode($country_codes_arr)?>;
function validatepmbform(id,invoice_field,payment_type,min_payment,invoice_text,show_tos)
{
  var fname=document.getElementById('pmbFnameID'+id);
  var lname=document.getElementById('pmbLnameID'+id);
  var address=document.getElementById('pmbAddrID'+id);
  var city=document.getElementById('pmbCityID'+id);
  var postcode=document.getElementById('pmbZipID'+id);
  var email=document.getElementById('pmbEmailID'+id);
  var phone=document.getElementById('pmbPhoneID'+id);
  var method=document.getElementById('pmbMethodID'+id);
  var state=document.getElementById('pmbStateID'+id);
  
  if(payment_type==1)
  {
    var amount=document.getElementById('payment_amount'+id);
    var min_amount=parseFloat(min_payment);
    var the_amount=parseFloat(amount.value)+0;
    if(isNaN(the_amount))
    {
      alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_NAN' ));?>');
      amount.focus();
      return false;
    }
    else if(the_amount<min_amount)
    {
      alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_LESS_AMNT' ));?>');
      amount.focus();
      return false;
    }
  }
  if(invoice_field==1)
  {
    var inv=document.getElementById('pmbInvID'+id);
    if(inv.value=='')
    {
      alert('Please input the '+invoice_text+' field');
      inv.focus();
      return false;
    }
  }
  if(fname.value=='')
  {
    alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_FNAME' ));?>');
    fname.focus();
    return false;
  }
  if(lname.value=='')
  {
    alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_LNAME' ));?>');
    lname.focus();
    return false;
  }
  if(address.value=='')
  {
    alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_ADDR' ));?>');
    address.focus();
    return false;
  }
  if(city.value=='')
  {
    alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_CITY' ));?>');
    city.focus();
    return false;
  }
  if(postcode.value=='')
  {
    alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_POSTCODE' ));?>');
    postcode.focus();
    return false;
  }
  if(state.value=='')
  {
    alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_STATE' ));?>');
    state.focus();
    return false;
  }
  if(email.value=='')
  {
    alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_EMAIL' ));?>');
    email.focus();
    return false;
  }
  if(!validateEmail(email.value))
  {
    alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_EMAIL_VALID' ));?>');
    email.focus();
    return false;
  }
  if(phone.value=='')
  {
    alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_PHONE' ));?>');
    phone.focus();
    return false;
  }

  if(method.value=='')
  {
    alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_PAYMENT_METHOD' ));?>');
    method.focus();
    return false;
  }
  if(show_tos==1)
  {
    var tos_chk=document.getElementById('pmbTosID'+id);
    if(tos_chk.checked==false)
    {
      alert('<?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_ERR_TOS' ));?>');
      tos_chk.focus();
      return false;
    }
  }
  return true;
}
function validateEmail(email) { 
  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}
function populatePmbForm(id)
{
  var fname=document.getElementById('pmbFnameID'+id);
  var lname=document.getElementById('pmbLnameID'+id);
  var address=document.getElementById('pmbAddrID'+id);
  var city=document.getElementById('pmbCityID'+id);
  var postcode=document.getElementById('pmbZipID'+id);
  var country=document.getElementById('pmbCountryID'+id);
  var email=document.getElementById('pmbEmailID'+id);
  var phone=document.getElementById('pmbPhoneID'+id);
  var method=document.getElementById('pmbMethodID'+id);
  var state=document.getElementById('pmbStateID'+id);
  
  fname.value=dfname;
  lname.value=dlname;
  address.value=daddress;
  city.value=dcity;
  postcode.value=dpostcode;
  state.value=dstate;
  
  if(dcountry!='')
  {
    country.value=dcountry;
  }
  email.value=demail;
  phone.value=dphone;
  if(dmethod!='')
  {
    method.value=dmethod;
  }
}
function pmbCancelFrm(id)
{
  var fname=document.getElementById('pmbFnameID'+id);
  var lname=document.getElementById('pmbLnameID'+id);
  var address=document.getElementById('pmbAddrID'+id);
  var city=document.getElementById('pmbCityID'+id);
  var postcode=document.getElementById('pmbZipID'+id);
  var country=document.getElementById('pmbCountryID'+id);
  var email=document.getElementById('pmbEmailID'+id);
  var phone=document.getElementById('pmbPhoneID'+id);
  var method=document.getElementById('pmbMethodID'+id);
  var state=document.getElementById('pmbStateID'+id);
  
  dfname=fname.value;
  dlname=lname.value;
  daddress=address.value;
  dcity=city.value;
  dpostcode=postcode.value;
  dcountry=country.value;
  demail=email.value;
  dphone=phone.value;
  dmethod=method.value;
  dstate=state.value;
}
function openSqueeze(id,width,height)
{
	var options = {size: {x: width, y: height}, 
  onClose: function(a){a.innerHTML='';},
  onOpen: function(a){},
  closable: false,
  closeBtn: false,
  };
  if(window['SqueezeBox'+id]=='')
  {
    window['SqueezeBox'+id]=SqueezeBox;
  }
  if(window['SqueezeBox'+id].options)
  {
    window['SqueezeBox'+id].options=null;
  }
	window['SqueezeBox'+id].initialize(options);
  var content='';
  if(window['pmbform'+id]=='')
  {
    content=document.getElementById('pmb_popup_content'+id).innerHTML;
    window['pmbform'+id]=content;
    document.getElementById('pmb_popup_content'+id).innerHTML='';
  }
  else
  {
    content=window['pmbform'+id];
  }
	window['SqueezeBox'+id].setContent('string',content);
  setTimeout("populatePmbForm("+id+")",500);
  return false;
}
var dfname='';
var dlname='';
var daddress='';
var dcity='';
var dpostcode='';
var demail='';
var dphone='';
var dcountry='';
var dmethod='';
var dstate='';
function pmbGetPopupContent(obj)
{
  var content='';
  content+='<form action="" method="POST" name="PMB'+obj.id+'" onSubmit="return validatepmbform(\''+obj.id+'\',\''+obj.invoice_field+'\',\''+obj.payment_type+'\',\''+obj.min_payment+'\',\''+obj.invoice_text.replace("'","\\'")+'\',\''+obj.show_tos+'\');">';
  content+='<input type="hidden" name="working_module" value="'+obj.id+'" />';
  content+='<input type="hidden" name="pmb_action" value="pay" />';
  content+='<table width="100%" class="pmb_popup_tbl">';
  content+='<tbody>';
  if(obj.invoice_field==1)
  {
    content+='<tr class="pmb_popup_row">';
    content+='<td>';
    content+='<label for="pmbInvID'+obj.id+'" class="pmb_popup_lbl">'+obj.invoice_text+'</label>';
    content+='</td>';
    content+='<td>';
    content+='<input type="text" class="pmb_popup_input" name="InvNr" id="pmbInvID'+obj.id+'" value="" />';
    content+='</td>';
    content+='</tr>';
  }
  if(obj.payment_type==1)
  {
    var currency_str='';
    if(obj.currency=='USD')
    {
      currency_str='$';
    }
    else if(obj.currency=='EUR')
    {
      currency_str='&euro;';
    }
    else
    {
      currency_str=obj.currency;
    }
    content+='<tr class="pmb_popup_row">';
    content+='<td>';
    content+='<label for="payment_amount'+obj.id+'" class="pmb_popup_lbl">'+obj.amount_text+'</label>';
    content+='</td>';
    content+='<td>';
    content+='<input type="text" class="pmb_popup_payment_input" name="payment_amount" id="payment_amount'+obj.id+'" value="'+obj.min_payment+'" />';
    content+='<span class="pmb_popup_curr_sym">'+currency_str+'</span>';
    content+='</td>';
    content+='</tr>';
  }
  content+='<tr class="pmb_popup_row">';
  content+='<td>';
  content+='<label for="pmbFnameID'+obj.id+'" class="pmb_popup_lbl"><?php echo str_replace("'","\\'",JText::_( 'PMB_MODULE_BILLFNAME' ));?></label>';
  content+='</td>';
  content+='<td>';
  content+='<input type="text" class="pmb_popup_input" name="BillFname" id="pmbFnameID'+obj.id+'" value="" />';
  content+='</td>';
  content+='</tr>';
  content+='<tr class="pmb_popup_row">';
  content+='<td>';
  content+='<label for="pmbLnameID'+obj.id+'" class="pmb_popup_lbl"><?php echo str_replace("'","\\'",JText::_ ('PMB_MODULE_BILLLNAME' ));?></label>';
  content+='</td>';  
  content+='<td>';
  content+='<input type="text" class="pmb_popup_input" name="BillLname" id="pmbLnameID'+obj.id+'" value="" />';
  content+='</td>';
  content+='</tr>';
  content+='<tr class="pmb_popup_row">';
  content+='<td>';
  content+='<label for="pmbAddrID'+obj.id+'" class="pmb_popup_lbl"><?php echo str_replace("'","\\'",JText::_ ('PMB_MODULE_BILLADDR' ));?></label>';
  content+='</td>';
  content+='<td>';
  content+='<input type="text" class="pmb_popup_input" name="BillAddr" id="pmbAddrID'+obj.id+'" value="" />';
  content+='</td>';
  content+='</tr>';
  content+='<tr class="pmb_popup_row">';
  content+='<td>';
  content+='<label for="pmbCityID'+obj.id+'" class="pmb_popup_lbl"><?php echo str_replace("'","\\'",JText::_ ('PMB_MODULE_BILLCITY' ));?></label>';
  content+='</td>';
  content+='<td>';
  content+='<input type="text" class="pmb_popup_input" name="BillCity" id="pmbCityID'+obj.id+'" value="" />';
  content+='</td>';
  content+='</tr>';
  content+='<tr class="pmb_popup_row">';
  content+='<td>';
  content+='<label for="pmbZipID'+obj.id+'" class="pmb_popup_lbl"><?php echo str_replace("'","\\'",JText::_ ('PMB_MODULE_BILLZIP' ));?></label>';
  content+='</td>';
  content+='<td>';
  content+='<input type="text" class="pmb_popup_input" name="BillZip" id="pmbZipID'+obj.id+'" value="" />';
  content+='</td>';
  content+='</tr>';
  content+='<tr class="pmb_popup_row">';
  content+='<td>';
  content+='<label for="pmbStateID'+obj.id+'" class="pmb_popup_lbl"><?php echo str_replace("'","\\'",JText::_ ('PMB_MODULE_BILLSTATE' ));?></label>';
  content+='</td>';
  content+='<td>';
  content+='<input type="text" class="pmb_popup_input" name="BillState" id="pmbStateID'+obj.id+'" value="" />';
  content+='</td>';
  content+='</tr>'; 
  content+='<tr class="pmb_popup_row">';
  content+='<td>';
  content+='<label for="pmbCountryID'+obj.id+'" class="pmb_popup_lbl"><?php echo str_replace("'","\\'",JText::_ ('PMB_MODULE_BILLCOUNTRY' ));?></label>';
  content+='</td>';
  content+='<td>';
  content+='<select id="pmbCountryID'+obj.id+'" name="BillCountry" class="pmb_popup_input_co">';
  for(var code in pmbCountries)
  {
    if(code==obj.default_country)
    {
      content+='<option selected="selected" value="'+code+'">'+pmbCountries[code]+'</option>';
    }
    else
    {
      content+='<option value="'+code+'">'+pmbCountries[code]+'</option>';
    }
  }
  content+='</select>';
  content+='</td>';
  content+='</tr>';
  content+='<tr class="pmb_popup_row">';
  content+='<td>';
  content+='<label for="pmbEmailID'+obj.id+'" class="pmb_popup_lbl"><?php echo str_replace("'","\\'",JText::_ ('PMB_MODULE_EMAIL' ));?></label>';
  content+='</td>';
  content+='<td>';
  content+='<input type="text" class="pmb_popup_input" name="BillEmail" id="pmbEmailID'+obj.id+'" value="" />';
  content+='</td>';
  content+='</tr>';
  content+='<tr class="pmb_popup_row">';
  content+='<td>';
  content+='<label for="pmbPhoneID'+obj.id+'" class="pmb_popup_lbl"><?php echo str_replace("'","\\'",JText::_ ('PMB_MODULE_PHONE' ));?></label>';
  content+='</td>';
  content+='<td>';
  content+='<input type="text" class="pmb_popup_input" name="BillPhone" id="pmbPhoneID'+obj.id+'" value="" />';
  content+='</td>';
  content+='</tr>';
  if(obj.payment_methods.length>1)
  {
    content+='<tr class="pmb_popup_row">';
    content+='<td>';
    content+='<label for="pmbMethodID'+obj.id+'" class="pmb_popup_lbl"><?php echo str_replace("'","\\'",JText::_ ('PMB_MODULE_PAYMENT_METHOD' ));?></label>';
    content+='</td>';
    content+='<td>';
    content+='<select id="pmbMethodID'+obj.id+'" name="BillMethod" class="pmb_popup_input_co">';
    content+='<option selected="selected" value=""><?php echo str_replace("'","\\'",JText::_ ('PMB_MODULE_PLEASE_SELECT' ));?></option>';
    for(var i=0;i<obj.payment_methods.length;i++)
    {
      content+='<option value="'+obj.payment_methods[i].name+'">'+obj.payment_methods[i].title+obj.payment_methods[i].fee+'</option>';
    }
    content+='</select>';
    content+='</td>';
    content+='</tr>';
  }
  else
  {
    content+='<input type="hidden" name="BillMethod" id="pmbMethodID'+obj.id+'" value="'+obj.payment_methods[0].name+'" />';
  }
  if(obj.show_tos==1)
  {
    content+='<tr class="pmb_popup_row">';
    content+='<td>';
    content+='<label for="pmbTosID'+obj.id+'" class="pmb_popup_lbl"><a class="pmb_tos_link" href="'+obj.tos_link+'" target="_blank">'+obj.tos_text+'</a></label>';
    content+='</td>';
    content+='<td>';
    content+='<input type="checkbox" class="pmb_popup_input" name="BillToS" id="pmbTosID'+obj.id+'" value="1" />';
    content+='</td>';
    content+='</tr>';
  }
  content+='<tr class="pmb_popup_submit_row">';
  content+='<td>';
  content+='<input style="float:left" class="pmb_submit_pay'+obj.btn_cancel_tpl+'" type="button" value="'+obj.popup_cancel_text+'" onClick="javascript:pmbCancelFrm('+obj.id+');SqueezeBox'+obj.id+'.close();" />';
  content+='</td>';
  content+='<td>';
  content+='<input class="pmb_submit_pay'+obj.btn_tpl+'" type="submit" value="'+obj.popup_button_text+'" />';
  content+='</td>';
  content+='</tr>';
  content+='</tbody>';
  content+='</table>';
  content+='</form>';
  document.getElementById('pmb_popup_cid'+obj.id).innerHTML=content;
  
}
