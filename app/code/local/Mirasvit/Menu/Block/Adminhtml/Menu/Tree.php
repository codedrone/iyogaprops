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


class Mirasvit_Menu_Block_Adminhtml_Menu_Tree extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{

    protected $_withProductCount;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('menu/tree.phtml');
        $this->setUseAjax(true);
        $this->_withProductCount = true;
    }

    protected function _prepareLayout()
    {
        $addMenuUrl = $this->getUrl("*/*/add", array(
            '_current' => true,
            'menu_id'  => null,
            '_query'   => false
        ));

        $this->setChild('add_menu_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('menu')->__('Add Menu'),
                    'onclick'   => "addNew('".$addMenuUrl."', false)",
                    'class'     => 'add',
                    'id'        => 'add_menu_button'
                ))
        );

        $addItemUrl = $this->getUrl("*/adminhtml_item/add", array(
            '_current' => true,
            'id'       => null,
            '_query'   => false
        ));

        $this->setChild('add_item_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('menu')->__('Add Item'),
                    'onclick'   => "addNew('".$addItemUrl."', false)",
                    'class'     => 'add',
                    'id'        => 'add_item_button'
                ))
        );

        $this->setChild('menu_switcher',
            $this->getLayout()->createBlock('menu/adminhtml_menu_switcher')
                ->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, '_query'=>false, 'store'=>null)))
                ->setTemplate('menu/switcher.phtml')
        );
        return parent::_prepareLayout();
    }

    public function getMenu()
    {
        return Mage::registry('current_menu');
    }

    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current' => true, 'id' => null,'store' => null);
        if (
            (is_null($expanded) && Mage::getSingleton('admin/session')->getIsTreeWasExpanded())
            || $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/categoriesJson', $params);
    }

    public function getNodesUrl()
    {
        return $this->getUrl('*/*/jsonTree');
    }

    public function getMoveUrl()
    {
        return $this->getUrl('*/adminhtml_item/move');
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/adminhtml_item/edit');
    }

    public function getTree()
    {
        $rootArray = $this->_getNodeJson($this->getMenu());
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    public function getTreeJson($parenNodeCategory=null)
    {
        $rootArray = $this->_getNodeJson($this->getMenu());
        $json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }
        $items = array();
        $ids   = explode('/', $path);

        foreach ($ids as $id) {
            $item        = Mage::getModel('menu/item')->load($id);
            $items[] = $this->_getNodeJson($item);
        }
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($items) . ';'
            . '</script>';
    }


    protected function _getNodeJson($node)
    {
        $item = array(
            'id'        => $node->getId(),
            'path'      => $node->getPath(),
            'cls'       => ($node->getIsActive() ? 'active' : 'no-active'),
            'text'      => $this->_buildNodeName($node),
            'icon'      => Mage::getDesign()->getSkinUrl('menu/images/'.$node->getType().'.png'),
            'allowDrag' => true,
            'allowDrop' => true,
        );

        if ($node->hasChildren()) {
            $item['children'] = array();
            foreach ($node->getChildren() as $child) {
                $item['children'][] = $this->_getNodeJson($child);
            }
            $item['expanded'] = true;
        }

        return $item;
    }

    protected function _buildNodeName($node)
    {
        $name = '';
        // $name .= $node->getPosition().'|'.$node->getLevel().'|'.$node->getPath().'|';

        $name .= $node->getName();

        return $name;
    }
}
