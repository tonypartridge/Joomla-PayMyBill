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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_paymybill/assets/css/paymybill.css');
$document->addStyleSheet(Uri::root() . 'media/com_paymybill/css/list.css');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_paymybill');
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_paymybill&task=payments.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'paymentList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>

<form action="<?php echo Route::_('index.php?option=com_paymybill&view=payments'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>

			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

			<div class="clearfix"></div>
			<table class="table table-striped" id="paymentList">
				<thead>
				<tr>
					<?php if (isset($this->items[0]->ordering)): ?>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<?php endif; ?>
					<th width="1%" >
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
					</th>
					
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'JGLOBAL_FIELD_ID_LABEL', 'a.id', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_ORDER_ID', 'a.order_id', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_STATUS', 'a.status', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_PAYMENT', 'a.payment', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_BILLFNAME', 'a.BillFname', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_BILLLNAME', 'a.BillLname', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_BILLADDR', 'a.BillAddr', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_BILLEMAIL', 'a.BillEmail', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_BILLPHONE', 'a.BillPhone', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_INVNR', 'a.InvNr', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_CDATE', 'a.cdate', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PAYMYBILL_PAYMENTS_MDATE', 'a.mdate', $listDirn, $listOrder); ?>
					</th>
					
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create', 'com_paymybill');
					$canEdit    = $user->authorise('core.edit', 'com_paymybill');
					$canCheckin = $user->authorise('core.manage', 'com_paymybill');
					$canChange  = $user->authorise('core.edit.state', 'com_paymybill');
					?>
					<tr class="row<?php echo $i % 2; ?>">

						<?php if (isset($this->items[0]->ordering)) : ?>
							<td class="order nowrap center hidden-phone">
								<?php if ($canChange) :
									$disableClassName = '';
									$disabledLabel    = '';

									if (!$saveOrder) :
										$disabledLabel    = Text::_('JORDERINGDISABLED');
										$disableClassName = 'inactive tip-top';
									endif; ?>
									<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
										  title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
									<input type="text" style="display:none" name="order[]" size="5"
										   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
								<?php else : ?>
									<span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
								<?php endif; ?>
							</td>
						<?php endif; ?>
						<td >
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>

						
						<td>
							<?php echo $item->id; ?>
						</td>
						<td>
							<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'payments.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_paymybill&task=payment.edit&id='.(int) $item->id); ?>">
								<?php echo $this->escape($item->order_id); ?></a>
							<?php else : ?>
								<?php echo $this->escape($item->order_id); ?>
							<?php endif; ?>
						</td>
						<td>
							<?php echo $item->status; ?>
						</td>
						<td>
							<?php echo $item->payment; ?>
						</td>
						<td>
							<?php echo $item->BillFname; ?>
						</td>
						<td>
							<?php echo $item->BillLname; ?>
						</td>
						<td>
							<?php echo $item->BillAddr; ?>
						</td>
						<td>
							<?php echo $item->BillEmail; ?>
						</td>
						<td>
							<?php echo $item->BillPhone; ?>
						</td>
						<td>
							<?php echo $item->InvNr; ?>
						</td>
						<td>
							<?php
								$date = $item->cdate;
								echo $date > 0 ? HTMLHelper::_('date', $date, JText::_('DATE_FORMAT_LC6')) : '-';
							?>
						</td>
						<td>
							<?php
								$date = $item->mdate;
								echo $date > 0 ? HTMLHelper::_('date', $date, JText::_('DATE_FORMAT_LC6')) : '-';
							?>
						</td>

					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
</form>
<script>
    window.toggleField = function (id, task, field) {

        var f = document.adminForm, i = 0, cbx, cb = f[ id ];

        if (!cb) return false;

        while (true) {
            cbx = f[ 'cb' + i ];

            if (!cbx) break;

            cbx.checked = false;
            i++;
        }

        var inputField   = document.createElement('input');

        inputField.type  = 'hidden';
        inputField.name  = 'field';
        inputField.value = field;
        f.appendChild(inputField);

        cb.checked = true;
        f.boxchecked.value = 1;
        Joomla.submitform(task);

        return false;
    };
</script>