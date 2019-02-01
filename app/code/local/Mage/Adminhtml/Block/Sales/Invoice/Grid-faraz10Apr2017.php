<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
/*function getOrdDetails($id){

	$sql = "select `created_at` from sales_flat_order where entity_id='".$id."'";
	$runQ = mysql_query($sql) or die(mysql_error());
	$getData = mysql_fetch_assoc($runQ);
	$balYP = $getData['created_at'];
 		return $balYP;
		
}*/
class Mage_Adminhtml_Block_Sales_Invoice_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_invoice_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_invoice_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
		//$collection->getSelect()->join('sales_flat_invoice_item', 'main_table.entity_id = sales_flat_invoice_item.parent_id',array('discount_amount'));
       // $collection->addFieldToFilter(array('entity_id'), array('1'));
		$this->setCollection($collection);
		//echo "<blink><strong style=\"color:red;\">This page is under progress, the results you are seeing are dummy or not correct</strong></blink>";
		//echo $collection->getSelect();
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Invoice #'),
            'index'     => 'increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Invoice Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));
		 /* $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Created'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ));
*/
        $this->addColumn('order_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_id',
            'type'      => 'text',
        ));
		
		$this->addColumn('discount_amount', array(
            'header'    => Mage::helper('sales')->__('Discount Amount'),
            'index'     => 'discount_amount',
           
        ));
			$this->addColumn('discount_description', array(
            'header'    => Mage::helper('sales')->__('Discount Desc'),
            'index'     => 'discount_description',
           
        ));
		

		$this->addColumn('shipping_amount', array(
            'header'    => Mage::helper('sales')->__('Shipping Amount'),
            'index'     => 'shipping_amount',
            
        ));
		$this->addColumn('shipping_incl_tax', array(
            'header'    => Mage::helper('sales')->__('Shipping Charges'),
            'index'     => 'shipping_incl_tax',
            
        ));


       /* $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));*/

        $this->addColumn('state', array(
            'header'    => Mage::helper('sales')->__('Status'),
            'index'     => 'state',
            'type'      => 'options',
            'options'   => Mage::getModel('sales/order_invoice')->getStates(),
        ));

        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('customer')->__('Amount'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'align'     => 'right',
            'currency'  => 'order_currency_code',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sales')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url'     => array('base'=>'*/sales_invoice/view'),
                        'field'   => 'invoice_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('invoice_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('PDF Invoices'),
             'url'  => $this->getUrl('*/sales_invoice/pdfinvoices'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/invoice')) {
            return false;
        }

        return $this->getUrl('*/sales_invoice/view',
            array(
                'invoice_id'=> $row->getId(),
            )
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
