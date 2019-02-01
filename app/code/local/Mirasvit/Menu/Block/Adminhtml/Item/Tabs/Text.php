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


class Mirasvit_Menu_Block_Adminhtml_Item_Tabs_Text extends Mirasvit_Menu_Block_Adminhtml_Item_Tabs
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (Mage::helper('menu')->isTabAllowed(Mirasvit_Menu_Model_Item_Type::TYPE_TEXT)) {
            $this->addTab('design', array(
                'label'   => Mage::helper('menu')->__('Custom Design'),
                'content' => $this->getLayout()->createBlock('menu/adminhtml_item_tab_text_design')->toHtml(),
            ));
        }

        return $this;
    }
}
