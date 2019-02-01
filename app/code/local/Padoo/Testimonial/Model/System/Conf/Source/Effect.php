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
 
class Padoo_Testimonial_Model_System_Conf_Source_Effect
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label'=>Mage::helper('adminhtml')->__('---Select One---')),
            array('value' => 'fade', 'label'=>Mage::helper('adminhtml')->__('Fade')),
			array('value' => 'fadeZoom', 'label'=>Mage::helper('adminhtml')->__('FadeZoom')),
			array('value' => 'cover', 'label'=>Mage::helper('adminhtml')->__('Cover')),
			array('value' => 'uncover', 'label'=>Mage::helper('adminhtml')->__('Uncover')),
			array('value' => 'shuffle', 'label'=>Mage::helper('adminhtml')->__('Shuffle')),
			array('value' => 'zoom', 'label'=>Mage::helper('adminhtml')->__('Zoom')),
			array('value' => 'wipe', 'label'=>Mage::helper('adminhtml')->__('Wipe')),
			array('value' => 'toss', 'label'=>Mage::helper('adminhtml')->__('Toss')),
            array('value' => 'turnDown', 'label'=>Mage::helper('adminhtml')->__('TurnDown')),
            array('value' => 'turnUp', 'label'=>Mage::helper('adminhtml')->__('TurnUp')),
            array('value' => 'scrollDown', 'label'=>Mage::helper('adminhtml')->__('ScrollDown')),
            array('value' => 'scrollUp', 'label'=>Mage::helper('adminhtml')->__('ScrollUp'))
        );
    }

}
