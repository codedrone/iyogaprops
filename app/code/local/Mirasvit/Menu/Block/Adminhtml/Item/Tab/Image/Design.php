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


class Mirasvit_Menu_Block_Adminhtml_Item_Tab_Image_Design extends Mage_Adminhtml_Block_Widget_Form
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $model = Mage::registry('current_item');

        $set = $form->addFieldset('design', array('legend'=>Mage::helper('menu')->__('Custom Design')));

        $set->addField('position', 'position', array(
            'label'  => Mage::helper('menu')->__('Position'),
            'name'   => 'attr[%s]',
            'top'    => $model->getAttr('top'),
            'right'  => $model->getAttr('right'),
            'bottom' => $model->getAttr('bottom'),
            'left'   => $model->getAttr('left'),
            'note'   => Mage::helper('menu')->__('Image absolute position ')
        ));

        $this->setForm($form);
    }

}

