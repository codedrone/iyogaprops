<?php
/**
 * Padoosoft Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0).
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you are unable to obtain it through the world-wide-web, please send
 * an email to support@mage-addons.com so we can send you a copy immediately.
 *
 * @category   Padoo
 * @package    Padoo_Testimonial
 * @author     PadooSoft Team
 * @copyright  Copyright (c) 2010-2012 Padoo Co. (http://mage-addons.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Padoo_Testimonial_Block_Adminhtml_Testimonial_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('testimonialGrid');
        $this->setDefaultSort('testimonial_position');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Padoo_Testimonial_Block_Adminhtml_Testimonial_Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getModel('padoo_testimonial/testimonial')->getCollection());
        return parent::_prepareCollection();
    }

    /**
     * Preparing colums for grid
     *
     * @return Padoo_Testimonial_Block_Adminhtml_Testimonial_Grid
     */
    protected function _prepareColumns()
    {
		$this->addColumn('testimonial_id', array(
			'header' => Mage::helper('padoo_testimonial')->__('ID'),
			'align' => 'center',
			'width' => '30px',
			'index' => 'testimonial_id',
		));
	
        $this->addColumn('testimonial_position', array(
            'header'    => Mage::helper('padoo_testimonial')->__('Position'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'testimonial_position',
            'type'      => 'number',
        ));

        $this->addColumn('testimonial_name', array(
            'header'    => Mage::helper('padoo_testimonial')->__('Name'),
            'align'     => 'left',
            'index'     => 'testimonial_name',
        ));
		
        $this->addColumn('testimonial_email', array(
            'header'    => Mage::helper('padoo_testimonial')->__('Email'),
            'align'     => 'left',
            'index'     => 'testimonial_email',
        ));

        $this->addColumn('testimonial_text', array(
            'header'    => Mage::helper('padoo_testimonial')->__('Text'),
            'align'     => 'left',
            'index'     => 'testimonial_text',
        ));
		
        $this->addColumn('testimonial_status', array(
            'header'    => Mage::helper('padoo_testimonial')->__('Status'),
            'align'     => 'center',
			'type' 		=> 'options',
            'index'     => 'testimonial_status',
			'options' => array(
				1 => 'Enabled',
				2 => 'Disabled',
			),
        ));
		
		$this->addColumn('action',
				array(
					'header' => Mage::helper('padoo_testimonial')->__('Action'),
					'width' => '80',
					'type' => 'action',
					'getter' => 'getId',
					'actions' => array(
						array(
							'caption' => Mage::helper('padoo_testimonial')->__('Edit'),
							'url' => array('base' => '*/*/edit'),
							'field' => 'id'
						)
					),
					'filter' => false,
					'sortable' => false,
					'index' => 'stores',
					'is_system' => true,
		));
		$this->addExportType('*/*/exportCsv', Mage::helper('padoo_testimonial')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('padoo_testimonial')->__('XML'));
		
        return parent::_prepareColumns();
    }

	protected function _prepareMassaction() {
		$this->setMassactionIdField('testimonial_id');
		$this->getMassactionBlock()->setFormFieldName('padoo_testimonial');
		$this->getMassactionBlock()->addItem('delete', array(
			'label' => Mage::helper('padoo_testimonial')->__('Delete'),
			'url' => $this->getUrl('*/*/massDelete'),
			'confirm' => Mage::helper('padoo_testimonial')->__('Are you sure?')
		));
		$statuses = Mage::getSingleton('padoo_testimonial/status')->getOptionArray();
			array_unshift($statuses, array('label' => '', 'value' => ''));
			$this->getMassactionBlock()->addItem('testimonial_status', array(
				'label' => Mage::helper('padoo_testimonial')->__('Change status'),
				'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
				'additional' => array(
					'visibility' => array(
						'name' => 'status',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => Mage::helper('padoo_testimonial')->__('Status'),
						'values' => $statuses
					)
				)
			));
		return $this;
	}
	
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
