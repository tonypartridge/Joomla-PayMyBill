<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Paymybill
 * @author     Tony Partridge <tony@xws.im>
 * @copyright  2021 Tony Partridge
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;


HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_paymybill/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'payment.cancel') {
			Joomla.submitform(task, document.getElementById('payment-form'));
		}
		else {
			
			if (task != 'payment.cancel' && document.formvalidator.isValid(document.id('payment-form'))) {
				
				Joomla.submitform(task, document.getElementById('payment-form'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_paymybill&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="payment-form" class="form-validate form-horizontal">

	
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'paymybill')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'paymybill', JText::_('COM_PAYMYBILL_TAB_PAYMYBILL', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_PAYMYBILL_FIELDSET_PAYMYBILL'); ?></legend>
				<?php echo $this->form->renderField('id'); ?>
				<?php echo $this->form->renderField('order_id'); ?>
				<?php echo $this->form->renderField('product_name'); ?>
				<?php echo $this->form->renderField('payment_method'); ?>
				<?php echo $this->form->renderField('mode'); ?>
				<?php echo $this->form->renderField('currency'); ?>
				<?php echo $this->form->renderField('status'); ?>
				<?php echo $this->form->renderField('payment'); ?>
				<?php echo $this->form->renderField('subtotal'); ?>
				<?php echo $this->form->renderField('trans_cost'); ?>
				<?php echo $this->form->renderField('trans_percent_cost'); ?>
				<?php echo $this->form->renderField('payment_type'); ?>
				<?php echo $this->form->renderField('BillFname'); ?>
				<?php echo $this->form->renderField('BillLname'); ?>
				<?php echo $this->form->renderField('BillAddr'); ?>
				<?php echo $this->form->renderField('BillCity'); ?>
				<?php echo $this->form->renderField('BillZip'); ?>
				<?php echo $this->form->renderField('BillState'); ?>
				<?php echo $this->form->renderField('BillCountry'); ?>
				<?php echo $this->form->renderField('BillEmail'); ?>
				<?php echo $this->form->renderField('BillPhone'); ?>
				<?php echo $this->form->renderField('InvNr'); ?>
				<?php echo $this->form->renderField('license'); ?>
				<?php echo $this->form->renderField('attachments'); ?>
				<?php echo $this->form->renderField('cdate'); ?>
				<?php echo $this->form->renderField('mdate'); ?>
			</fieldset>
		</div>
	</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<input type="hidden" name="jform[payment_title]" value="<?php echo $this->item->payment_title; ?>" />

	

	<?php $this->ignore_fieldsets = array('general', 'info', 'detail', 'jmetadata', 'item_associations', 'accesscontrol'); ?>
	<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>
	
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</form>
