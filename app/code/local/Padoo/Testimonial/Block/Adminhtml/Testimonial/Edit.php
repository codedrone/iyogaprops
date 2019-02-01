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
 
class Padoo_Testimonial_Block_Adminhtml_Testimonial_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'padoo_testimonial';
        $this->_controller = 'adminhtml_testimonial';

        $this->_updateButton('save', 'label', Mage::helper('padoo_testimonial')->__('Save Testimonial'));
        $this->_updateButton('delete', 'label', Mage::helper('padoo_testimonial')->__('Delete Testimonial'));

        if( $this->getRequest()->getParam($this->_objectId) ) {
            $model = Mage::getModel('padoo_testimonial/testimonial')->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('padoo_testimonial', $model);
        }
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if( Mage::registry('padoo_testimonial') && Mage::registry('padoo_testimonial')->getId() ) {
            return Mage::helper('padoo_testimonial')->__('Edit Testimonial');
        } else {
            return Mage::helper('padoo_testimonial')->__('Add Testimonial');
        }
    }
}
