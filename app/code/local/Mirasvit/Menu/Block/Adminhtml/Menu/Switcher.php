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


class Mirasvit_Menu_Block_Adminhtml_Menu_Switcher extends Mage_Adminhtml_Block_Template
{
    protected $_menuVarName = 'menu_id';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('menu/switcher.phtml');
        $this->setUseConfirm(true);
        $this->setUseAjax(true);
    }

    public function getMenuCollection()
    {
       return Mage::getModel('menu/menu')->getCollection();
    }

    public function getMenuId()
    {
        return $this->getRequest()->getParam($this->_menuVarName);
    }

    public function getSwitchUrl()
    {
        return $this->getUrl('*/*/*', array('_current' => false, $this->_menuVarName => null));
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }
}
