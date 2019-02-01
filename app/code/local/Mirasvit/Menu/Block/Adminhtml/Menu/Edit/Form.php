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


class Mirasvit_Menu_Block_Adminhtml_Menu_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_additionalButtons = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('menu/edit/form.phtml');
    }

    protected function _prepareLayout()
    {
        $menuId = $this->getMenuId();

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('menu')->__('Save Menu'),
                    'onclick' => "menuSubmit('" . $this->getSaveUrl() . "', true)",
                    'class'   => 'save'
                ))
        );

        if ($menuId) {
            $this->setChild('delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'   => Mage::helper('menu')->__('Delete Menu'),
                        'onclick' => "menuDelete('" . $this->getUrl('*/*/delete', array('_current' => true)) . "', true, {$menuId})",
                        'class'   => 'delete'
                    ))
            );
        }

        $this->setChild('tabs',
            $this->getLayout()->createBlock('menu/adminhtml_menu_tabs', 'tabs')
        );
        return parent::_prepareLayout();
    }

    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    public function getMenu()
    {
        return Mage::registry('current_menu');
    }

    public function getMenuId()
    {
        if ($this->getMenu()) {
            return $this->getMenu()->getId();
        }
    }

    public function getHeader()
    {
        if ($this->getMenuId()) {
            return $this->getMenu()->getName();
        } else {
            return Mage::helper('menu')->__('New Menu');
        }
    }

    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * Return URL for refresh input element 'path' in form
     *
     * @param array $args
     * @return string
     */
    public function getRefreshPathUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }

    public function getProductsJson()
    {
        $products = $this->getCategory()->getProductsPosition();
        if (!empty($products)) {
            return Mage::helper('core')->jsonEncode($products);
        }
        return '{}';
    }

    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
}
