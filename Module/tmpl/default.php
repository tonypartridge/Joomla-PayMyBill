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
if(isset($_POST['pmb_action'])&&$_POST['pmb_action']=='pay')
{
  if(isset($_POST['working_module'])&&$_POST['working_module']!=$module->id)
  {
    return;//skip other instances of the module
  }
}

JHTML::_('behavior.modal');
$document=JFactory::getDocument();
$document->addStyleSheet( juri::base().'modules/mod_paymybill/css/default.css' );
$document->addScript(juri::base().'modules/mod_paymybill/js/pmb.js.php');
/*
$params1  = getPMBModParams($module->id);
$dchk=$params1['dchk'];
$lic=$params1['pmblic'];
$the_domain=getPMBDomain();
if($the_domain)
{
  $chk=md5($lic.$the_domain);
  if($dchk!=$chk){return false;}
}
*/
$PMBHelper = new PMBHelper();

//get active payment methods
$payment_methods=array();
$pmb_params=json_decode($module->params);
$payment_method=array();
$get_method=0;
$method_name='';
$currency_error=0;
$currency="";
foreach($pmb_params as $key => $val)
{
  $pos = strpos($key, 'pmb_start_');
  if($pos!==false)
  {
    $get_method=1;
    $tarr=array();
    $tarr=explode("_",$key);
    $method_name=$tarr[2];
  }
  if($get_method)
  {
    if($key==$method_name.'_ordering')
    {
      $key='ordering';
      $val+=0;
    }
    if(!isset($payment_method[$key]))
    {
      $payment_method[$key]='';
    }
    $payment_method[$key]=$val;
  }
  $pos = strpos($key, 'pmb_end_');
  if($pos!==false)
  {
    $get_method=0;
    if(!isset($payment_method['name']))
    {
      $payment_method['name']='';
    }
    $payment_method['name']=$method_name;
    if($payment_method[$method_name.'_active']==1)
    {
      $payment_methods[]=$payment_method;
    }
    $method_name='';
    $payment_method=array();
  }
}
//check if currencies differ
$currencies=array();
if(count($payment_methods)>0)
{
  foreach($payment_methods as $method)
  {
    $currencies[]=$method[$method['name']."_currency"];
  }
  $currencies=array_unique($currencies);
  if(count($currencies)>1)
  {
    $currency_error=1;
  }
  else
  {
    $currency=$currencies[0];
  }
}

//sort payment methods
if(count($payment_methods)>0)
{
  $payment_methods=$PMBHelper->sortPaymentMethods($payment_methods);
}

?>


<div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
<?php

$payment_method_missing=JText::_( 'PMB_MODULE_ERR_NO_PAYMENT_METHOD' );
$currencies_differ=JText::_( 'PMB_MODULE_ERR_CURRENCIES' );
$payment_missing=JText::_( 'PMB_MODULE_ERR_PAYMENT' );
$product_name_missing=JText::_( 'PMB_MODULE_ERR_PRODUCT_NAME' );
$tos_link_missing=JText::_( 'PMB_MODULE_TOS_LINK_ERR' );
$attach_missing=JText::_( 'PMB_MODULE_ATTACH_ERR' );
$attach2_missing=JText::_( 'PMB_MODULE_ATTACH2_ERR' );

$invoice_field=$params->get('show_inv_field');
$invoice_text=str_replace('"','&quot;',$params->get('inv_text'));
$payment=$params->get('payment');
$payment_type=$params->get('payment_type');
$payment_description=$params->get('payment_description');
$popup_description=$params->get('popup_description');
$button_text=str_replace('"','&quot;',$params->get('button_text'));
$popup_button_text=str_replace('"','&quot;',$params->get('popup_button_text'));
$popup_cancel_text=str_replace('"','&quot;',$params->get('popup_cancel_text'));
$default_country=($params->get('default_country')!="")?strtoupper($params->get('default_country')):"US";
$payment_type=$params->get('payment_type');
$amount_text=$params->get('amount_text');
$min_payment=$params->get('min_payment');
$product_name=$params->get('product_name');
$btn_tpl="_".$params->get('pay_btn_tpl');
$btn_cancel_tpl="_".$params->get('cancel_btn_tpl');
$show_tos=$params->get('show_tos');
$tos_link=$params->get('tos_link');
$tos_text=str_replace('"','&quot;',$params->get('tos_text'));
$popup_width=($params->get('popup_width')+0)>0?$params->get('popup_width'):420;
$popup_height=($params->get('popup_height')+0)>0?$params->get('popup_height'):440;
$attach=$params->get('attach');
$attach2=$params->get('attach2');
$attach_err=0;
$attach2_err=0;
if($attach!=-1 && $attach!="")
{
  if(!is_file(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."documents".DS.$attach))
  {
    $attach_err=1;
  }
}
if($attach2!="")
{
  $tarr=explode(",",$attach2);
  foreach($tarr as $t)
  {
    if(!is_file(JPATH_BASE.DS."modules".DS."mod_paymybill".DS."documents".DS.trim(trim($t,DS))))
    {
      $attach2_err=1;
    }
  }
}
$errors_present=0;
if(count($payment_methods)==0)
{
  $errors_present=1;
  ?>
    <div class="pmb_error"><?php echo $payment_method_missing;?></div>
  <?php
}
if($currency_error)
{
  $errors_present=1;
  ?>
    <div class="pmb_error"><?php echo $currencies_differ;?></div>
  <?php
}
if($attach_err)
{
  $errors_present=1;
  ?>
    <div class="pmb_error"><?php echo $attach_missing;?></div>
  <?php
}
if($attach2_err)
{
  $errors_present=1;
  ?>
    <div class="pmb_error"><?php echo $attach2_missing;?></div>
  <?php
}
if($errors_present==1)
{
  echo '</div>';
  return;
}
//prechecks for payment method settings
$popup_payment_methods=array();
if(count($payment_methods)>0)
{
  foreach($payment_methods as $method)
  {
    require_once(JPATH_BASE.DS.'modules'.DS."mod_paymybill".DS."methods".DS.$method['name'].".php");
    $method_class="PMB".$method['name'];
    $working_method=new $method_class;
    if(!$working_method->checkMethod($params))
    {
      echo '</div>';
      return;
    }
    else
    {
      $fee=$method[$method['name'].'_trans_cost']+0;
      $percent=$method[$method['name'].'_trans_percent_cost']+0;
      $fee_str=" (";
      $fee_arr=array();
      if($fee>0)
      {
        $fee_arr[]="+$fee";
      }
      if($percent>0)
      {
        $percent = round((float)$percent * 100 ) . '%';
        $fee_arr[]="+$percent";
      }
      $fee_str.=implode(", ",$fee_arr).")";
      if(count($fee_arr)==0)
      {
        $fee_str="";
      }
      $popup_payment_methods[]=array('name'=>$method['name'],'title'=>$method[$method['name']."_title"],'fee'=>$fee_str);
    }
  }
}

if($product_name=='')
{
  ?>
    <div class="pmb_error"><?php echo $product_name_missing;?></div>
  <?php
}
else if(($payment=='' || ($payment+0)==0) && $payment_type==0)
{
  ?>
    <div class="pmb_error"><?php echo $payment_missing;?></div>
  <?php
}
else if($show_tos && $tos_link=='')
{
  ?>
    <div class="pmb_error"><?php echo $tos_link_missing;?></div>
  <?php
}
else
{
  //seams ok
  $action=isset($_REQUEST['pmb_action'])?$_REQUEST['pmb_action']:"";
  if($action=='pay')//payment action
  {
    $error_check=0;
    $payment_amount=isset($_POST['payment_amount'])?$_POST['payment_amount']:0;
    $BillFname=isset($_POST['BillFname'])?$_POST['BillFname']:"";
    $BillLname=isset($_POST['BillLname'])?$_POST['BillLname']:"";
    $BillAddr=isset($_POST['BillAddr'])?$_POST['BillAddr']:"";
    $BillCity=isset($_POST['BillCity'])?$_POST['BillCity']:"";
    $BillZip=isset($_POST['BillZip'])?$_POST['BillZip']:"";
    $BillCountry=isset($_POST['BillCountry'])?$_POST['BillCountry']:"";
    $BillEmail=isset($_POST['BillEmail'])?$_POST['BillEmail']:"";
    $BillPhone=isset($_POST['BillPhone'])?$_POST['BillPhone']:"";
    $InvNr=isset($_POST['InvNr'])?$_POST['InvNr']:"";
    $BillToS=isset($_POST['BillToS'])?$_POST['BillToS']:"";
    $BillMethod=isset($_POST['BillMethod'])?$_POST['BillMethod']:"";
    $BillState=isset($_POST['BillState'])?$_POST['BillState']:"";
    
    if($payment_type==1)
    {
      $payment=$payment_amount+0;
      if($payment==0 || $payment<$min_payment)
      {
        $error_check=1;
      }
    }
    if($payment==0)
    {
      $error_check=1;
    }
    if($BillFname=='')
    {
      $error_check=1;
    }
    if($BillLname=='')
    {
      $error_check=1;
    }
    if($BillAddr=='')
    {
      $error_check=1;
    }
    if($BillCity=='')
    {
      $error_check=1;
    }
    if($BillZip=='')
    {
      $error_check=1;
    }
    if($BillState=='')
    {
      $error_check=1;
    }
    if($BillCountry=='')
    {
      $error_check=1;
    }
    if($BillPhone=='')
    {
      $error_check=1;
    }
    if($invoice_field && $InvNr=='')
    {
      $error_check=1;
    }
    if($BillMethod=='')
    {
      $error_check=1;
    }
    if($show_tos && $BillToS!='1')
    {
      $error_check=1;
    }
    if($error_check)
    {
      //error here
      ?>
      <div class="pmb_submit_error"><?php echo JText::_( 'PMB_MODULE_SUBMIT_ERROR' );?></div>
      <?php
      $action="";
    }
    else
    {
      //all good submit the form
      
      $payment+=0;
      $order_id=$PMBHelper->getNewOrderID($module->id);
      $details=array(
      'payment'=>$payment,
      'BillFname'=>$BillFname,
      'BillLname'=>$BillLname,
      'BillAddr'=>$BillAddr,
      'BillCity'=>$BillCity,
      'BillZip'=>$BillZip,
      'BillState'=>$BillState,
      'BillCountry'=>$BillCountry,
      'BillEmail'=>$BillEmail,
      'BillPhone'=>$BillPhone,
      'InvNr'=>$InvNr,
      'order_id'=>$order_id
      );
      if(count($payment_methods)>0)
      {
        foreach($payment_methods as $method)
        {
          
          if($method['name']==$BillMethod)
          {
            $method_class="PMB".$method['name'];
            $working_method=new $method_class;
            $all_params=$PMBHelper->getWorkingModParams($module->id);
            $working_method->doPayment($params,$details,$all_params);
          }
        }
      }
    }
  }
  if($action=='')//normal action
  {
  ?>
    <div id="pmb_popup_content<?php echo $module->id;?>" class="pmb_popup_content" style="display:none;">
      <?php
      if(trim($popup_description)!="")
      {
      ?>
      <div class="pmb_popup_head">
        <?php echo $popup_description;?>
      </div>
      <?php
      }
      ?>
      <div class="pmb_popup_content" id="pmb_popup_cid<?php echo $module->id;?>">
        
      </div>
    </div>
    <div class="pmb_wrp">
      <?php
      if(trim($payment_description)!="")
      {
      ?>
      <div class="pmb_description">
        <?php echo $payment_description;?>
      </div>
      <?php
      }
      ?>
      <input class="pmb_submit_pay<?php echo $btn_tpl;?>" style="float:none;" type="button" value="<?php echo $button_text;?>" onClick="return openSqueeze('<?php echo $module->id;?>',<?php echo $popup_width;?>,<?php echo $popup_height;?>);" />
    </div>
    <?php
    //get the object to send to js function to pupulate popup content
    $popup=array(
    'id'=>$module->id,
    'invoice_field'=>$invoice_field,
    'payment_type'=>$payment_type,
    'min_payment'=>$min_payment,
    'invoice_text'=>$invoice_text,
    'currency'=>$currency,
    'amount_text'=>$amount_text,
    'default_country'=>$default_country,
    'btn_cancel_tpl'=>$btn_cancel_tpl,
    'btn_tpl'=>$btn_tpl,
    'popup_button_text'=>$popup_button_text,
    'popup_cancel_text'=>$popup_cancel_text,
    'show_tos'=>$show_tos,
    'tos_link'=>$tos_link,
    'tos_text'=>$tos_text,
    'payment_methods'=>$popup_payment_methods
    );
    $pmb_js="
    var pmbform".$module->id."='';
    var SqueezeBox".$module->id."='';
    (function() {
      var pmbcontent=".json_encode($popup).";
    	pmbGetPopupContent(pmbcontent);
    })();
    ";
    ?>
    <script type="text/javascript">
    <!--
    <?php echo $pmb_js;?>
    //-->
    </script>
    <?
  }
}

?>
</div>
