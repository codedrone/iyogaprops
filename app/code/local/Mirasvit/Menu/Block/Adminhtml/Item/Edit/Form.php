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


class Mirasvit_Menu_Block_Adminhtml_Item_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('menu/item/edit/form.phtml');
    }

    protected function _prepareLayout()
    {
        $itemId = $this->getItemId();

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('menu')->__('Save Item'),
                    'onclick' => "itemSubmit('" . $this->getSaveUrl() . "', true)",
                    'class'   => 'save'
                ))
        );

        if ($itemId) {
            $this->setChild('delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'   => Mage::helper('menu')->__('Delete Item'),
                        'onclick' => "itemDelete('" . $this->getUrl('*/*/delete', array('_current' => true)) . "', true, {$itemId})",
                        'class'   => 'delete'
                    ))
            );
        }

        if ($this->getItem()->getType()) {
            $this->setChild('tabs',
                $this->getLayout()->createBlock('menu/adminhtml_item_tabs_'.$this->getItem()->getType(), 'tabs')
            );
        } else {
            $this->setChild('tabs',
                $this->getLayout()->createBlock('menu/adminhtml_item_tabs', 'tabs')
            );
        }

        return parent::_prepareLayout();
    }

    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    public function getItem()
    {
        return Mage::registry('current_item');
    }

    public function getItemId()
    {
        if ($this->getItem()) {
            return $this->getItem()->getId();
        }
    }

    public function getHeader()
    {
        if ($this->getItemId()) {
            return $this->getItem()->getName();
        } else {
            return Mage::helper('menu')->__('New Item');
        }
    }

    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
}
