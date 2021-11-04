<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Paymybill
 * @author     Tony Partridge <tony@xws.im>
 * @copyright  2021 Tony Partridge
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\Utilities\ArrayHelper;
/**
 * Methods supporting a list of Paymybill records.
 *
 * @since  1.6
 */
class PaymybillModelPayments extends \Joomla\CMS\MVC\Model\ListModel
{
    
        
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'payment_title', 'a.payment_title',
				'id', 'a.id',
				'order_id', 'a.order_id',
				'product_name', 'a.product_name',
				'payment_method', 'a.payment_method',
				'mode', 'a.mode',
				'currency', 'a.currency',
				'status', 'a.status',
				'payment', 'a.payment',
				'subtotal', 'a.subtotal',
				'trans_cost', 'a.trans_cost',
				'trans_percent_cost', 'a.trans_percent_cost',
				'payment_type', 'a.payment_type',
				'BillFname', 'a.BillFname',
				'BillLname', 'a.BillLname',
				'BillAddr', 'a.BillAddr',
				'BillCity', 'a.BillCity',
				'BillZip', 'a.BillZip',
				'BillState', 'a.BillState',
				'BillCountry', 'a.BillCountry',
				'BillEmail', 'a.BillEmail',
				'BillPhone', 'a.BillPhone',
				'InvNr', 'a.InvNr',
				'license', 'a.license',
				'attachments', 'a.attachments',
				'cdate', 'a.cdate',
		'cdate.from', 'cdate.to',
				'mdate', 'a.mdate',
		'mdate.from', 'mdate.to',
			);
		}

		parent::__construct($config);
	}

    
        
    
        

        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
        // List state information.
        parent::populateState('id', 'DESC');

        $context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $context);

        JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
        // Split context into component and optional section
        $parts = FieldsHelper::extract($context);

        if ($parts)
        {
            $this->setState('filter.component', $parts[0]);
            $this->setState('filter.section', $parts[1]);
        }
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

                
                    return parent::getStoreId($id);
                
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__paymybill` AS a');
                
                

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.order_id LIKE ' . $search . '  OR  a.BillFname LIKE ' . $search . '  OR  a.BillLname LIKE ' . $search . '  OR  a.BillAddr LIKE ' . $search . '  OR  a.BillEmail LIKE ' . $search . '  OR  a.BillPhone LIKE ' . $search . '  OR  a.InvNr LIKE ' . $search . ' )');
			}
		}
                

		// Filtering cdate
		$filter_cdate_from = $this->state->get("filter.cdate.from");

		if ($filter_cdate_from !== null && !empty($filter_cdate_from))
		{
			$query->where("a.`cdate` >= '".$db->escape($filter_cdate_from)."'");
		}
		$filter_cdate_to = $this->state->get("filter.cdate.to");

		if ($filter_cdate_to !== null  && !empty($filter_cdate_to))
		{
			$query->where("a.`cdate` <= '".$db->escape($filter_cdate_to)."'");
		}

		// Filtering mdate
		$filter_mdate_from = $this->state->get("filter.mdate.from");

		if ($filter_mdate_from !== null && !empty($filter_mdate_from))
		{
			$query->where("a.`mdate` >= '".$db->escape($filter_mdate_from)."'");
		}
		$filter_mdate_to = $this->state->get("filter.mdate.to");

		if ($filter_mdate_to !== null  && !empty($filter_mdate_to))
		{
			$query->where("a.`mdate` <= '".$db->escape($filter_mdate_to)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		//XXX_CUSTOM_ORDER_FOR_NESTED

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
                

		return $items;
	}
}
