<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Menu Manager Pro
 * @version   1.0.5
 * @revision  132
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */




class Mirasvit_Menu_Model_System_Config_Source_Position
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'none', 'label' => Mage::helper('menu')->__('Not Display')),
            array('value' => 'top', 'label' => Mage::helper('menu')->__('Top')),
            array('value' => 'left', 'label' => Mage::helper('menu')->__('Left')),
            array('value' => 'right', 'label' => Mage::helper('menu')->__('Right')),
            array('value' => 'footer', 'label' => Mage::helper('menu')->__('Footer')),
            array('value' => 'top.links', 'label' => Mage::helper('menu')->__('top.links'))
        );
    }
}
