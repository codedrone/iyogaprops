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
 
class Padoo_Testimonial_Block_Testimonial extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
		$collection = Mage::getModel("padoo_testimonial/testimonial")->getCollection();
		$collection->addFieldToFilter('testimonial_status','1');
        if ($this->getSidebar()){
            $collection->addFieldToFilter('testimonial_sidebar', '1');
        }

        $collection->setOrder('testimonial_position', 'ASC')
                   ->load();
        
        $this->setTestimonials($collection);
    }

	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
        $pager->setCollection($this->getTestimonials());
        $this->setChild('pager', $pager);
		$this->getTestimonials()->load();
        return $this;
    }
	
	 public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
	
}