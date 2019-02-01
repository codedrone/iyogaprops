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


class Mirasvit_Menu_Block_Adminhtml_Menu_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(Mage::helper('catalog')->__('Category Data'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    protected function _prepareLayout()
    {

        $this->addTab('general', array(
            'label'   => Mage::helper('menu')->__('General'),
            'content' => $this->getLayout()->createBlock(
                'menu/adminhtml_menu_tab_general',
                'menu.edit.general'
            )->toHtml(),
        ));

        return parent::_prepareLayout();
    }
}
