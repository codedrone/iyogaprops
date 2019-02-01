<?php
class Ost_Instagramslider_Model_Dimension
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('50x50')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('100x100')),
            array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('150x150')),
            array('value' => 4, 'label'=>Mage::helper('adminhtml')->__('200x200')),
            array('value' => 5, 'label'=>Mage::helper('adminhtml')->__('350x350')),
            array('value' => 6, 'label'=>Mage::helper('adminhtml')->__('640x640')),
            array('value' => 7, 'label'=>Mage::helper('adminhtml')->__('1080x1080')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            1 => Mage::helper('adminhtml')->__('50x50'),
            2 => Mage::helper('adminhtml')->__('100x100'),
            3 => Mage::helper('adminhtml')->__('150x150'),
            4 => Mage::helper('adminhtml')->__('200x200'),
            5 => Mage::helper('adminhtml')->__('350x350'),
            6 => Mage::helper('adminhtml')->__('640x640'),
            7 => Mage::helper('adminhtml')->__('1080x1080'),
        );
    }

}
