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




class Mirasvit_Menu_Adminhtml_MenuController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        Mage::getDesign()->setTheme('mirasvit');
        return $this;
    }

    protected function _initAction()
    {
        $this->_title($this->__('Menu'));
        return $this;
    }

    protected function _initMenu()
    {
        $menuId = (int) $this->getRequest()->getParam('menu_id', false);

        if (substr($this->getRequest()->getParam('id'), 0, 4) == 'menu') {
            $menuId = substr($this->getRequest()->getParam('id'), 5);
        }

        $menu = Mage::getModel('menu/menu');
        if ($menuId) {
            $menu->load($menuId);

            Mage::register('current_menu', $menu);
        }

        return $menu;
    }

    public function indexAction()
    {
        $menu = $this->_initMenu();

        $this->_title($this->__('Menu Manager'));
        $this->_initAction();

        if (!$menu->getId() && Mage::getModel('menu/menu')->getCollection()->count() > 0) {
            $menu = Mage::getModel('menu/menu')->getCollection()->getFirstItem();
            $this->_redirect('*/*/index', array('_current' => false, 'menu_id' => $menu->getId()));
        }

        $this->loadLayout()->_setActiveMenu('catalog');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setCanLoadTinyMce(true);
        $this->renderLayout();
    }

    public function addAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $params['_current'] = true;
        $redirect           = false;

        $menu = $this->_initMenu();

        $this->_title($menu->getId() ? $menu->getName() : $this->__('New Menu'));

        if ($this->getRequest()->getQuery('isAjax')) {
            $this->loadLayout();

            $eventResponse = new Varien_Object(array(
                'content'  => $this->getLayout()->getBlock('menu.edit')->getFormHtml()
                    .$this->getLayout()->getBlock('menu.tree')->getBreadcrumbsJavascript('', 'editingItemBreadcrumbs'),
                'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
            ));

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode($eventResponse->getData())
            );

            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('catalog');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->renderLayout();
    }

    public function saveAction()
    {
        $needRedirect = false;

        if ($data = $this->getRequest()->getPost()) {
            $menu = $this->_initMenu();

            if (!$menu->getId()) {
                $needRedirect = true;
            }

            $menu->addData($data);
            $menu->save();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('menu')->__('The menu has been saved.'));
        }


        $this->_redirect('*/*/index', array('_current' => false, 'menu_id' => $menu->getId()));
    }

    public function menuJsonAction()
    {
        $this->_initMenu();

        if ($itemId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $itemId);

            if (!$item = $this->_initItem()) {
                return;
            }

            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('menu/adminhtml_menu_tree')
                    ->getTreeJson($item)
            );
        }
    }

    public function deleteAction()
    {
        $menu = $this->_initMenu();
        $menu->delete();

        $this->_redirect('*/*/');
    }
}