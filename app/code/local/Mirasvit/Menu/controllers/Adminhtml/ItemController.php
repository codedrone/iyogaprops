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


class Mirasvit_Menu_Adminhtml_ItemController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        Mage::getDesign()->setTheme('mirasvit');
        return $this;
    }

    protected function _initMenu()
    {
        $this->_title($this->__('Menu'));

        $menuId = (int) $this->getRequest()->getParam('menu_id', false);
        $menu   = Mage::getModel('menu/menu');

        if ($menuId) {
            $menu->load($menuId);
        }

        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
        }

        Mage::register('current_menu', $menu);

        return $menu;
    }

    protected function _initItem()
    {
        $itemId = (int) $this->getRequest()->getParam('id', false);
        $item   = Mage::getModel('menu/item');

        if ($itemId) {
            $item->load($itemId);
        }

        Mage::register('current_item', $item);

        return $item;
    }

    public function addAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $params['_current'] = true;
        $redirect           = false;

        if (substr($this->getRequest()->getParam('id'), 0, 4) == 'menu') {
            $this->_forward('edit', 'adminhtml_menu');
            return;
        }
        $this->_initMenu();
        if (!($item = $this->_initItem())) {
            return;
        }

        $itemId = $item->getId();

        $this->_title($itemId ? $item->getName() : $this->__('New Item'));

        if ($this->getRequest()->getQuery('isAjax')) {
            $this->loadLayout();
            $breadcrumbsPath = $item->getPath();
            if (empty($breadcrumbsPath)) {
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    }
                    else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }

            $eventResponse = new Varien_Object(array(
                'content'  => $this->getLayout()->getBlock('item.edit')->getFormHtml()
                    .$this->getLayout()->getBlock('menu.tree')->getBreadcrumbsJavascript($breadcrumbsPath, 'editingItemBreadcrumbs'),
                'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
            ));

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode($eventResponse->getData())
            );

            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        $itemModel = $this->_initItem();
        $data      = $this->getRequest()->getParams();

        $itemModel->addData($data);

        try {
            if (!$itemModel->getId()) {
                $parentId = $this->getRequest()->getParam('parent_id');
                if (!$parentId || substr($parentId, 0, 4) == 'menu') {
                    $parentId = null;
                    $itemModel->setParentId(null);
                }

                $parentItem = Mage::getModel('menu/item')->load($parentId);
                switch ($itemModel->getType()) {
                    case Mirasvit_Menu_Model_Item_Type::TYPE_COLUMN:
                        if ($parentItem->getType() != Mirasvit_Menu_Model_Item_Type::TYPE_ROW) {
                            $rowModel = Mage::getModel('menu/item');
                            $rowModel->setType(Mirasvit_Menu_Model_Item_Type::TYPE_ROW)
                                ->setName('Row')
                                ->setParentId($parentId)
                                ->setPath($parentItem->getPath())
                                ->setIsActive(1)
                                ->setMenuId($itemModel->getMenuId())
                                ->save();
                            $parentItem = $rowModel;
                            $itemModel->setParentId($parentItem->getId());
                        }
                        break;

                    default:
                        break;
                }

                $itemModel->setPath($parentItem->getPath());
            }

            $itemModel->save();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $itemModel->getId()));
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, true);</script>'
        );
    }

    public function deleteAction()
    {
        $itemModel = $this->_initItem();
        try {
            $itemModel->delete();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

            $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $itemModel->getId()));
            $this->getResponse()->setBody(
                '<script type="text/javascript">parent.updateContent("' . $url . '", {}, true);</script>'
            );
        }
    }

    public function moveAction()
    {
        $itemModel = $this->_initItem();
        if (!$itemModel) {
            $this->getResponse()->setBody(Mage::helper('menu')->__('Item move error'));
            return;
        }

        $parentNodeId = $this->getRequest()->getPost('pid', false);
        $prevNodeId   = $this->getRequest()->getPost('aid', false);

        if (substr($parentNodeId, 0, 4) == 'menu') {
            $parentNodeId = null;
        }

        try {
            $itemModel->save();
            $itemModel->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody("SUCCESS");
        } catch (Exception $e){
            $this->getResponse()->setBody($e->getMessage());
        }
    }
}