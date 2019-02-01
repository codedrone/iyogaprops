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


class Mirasvit_Menu_Block_Menu extends Mage_Page_Block_Template_Links
{
    protected $_currentItemPath    = null;
    protected $_itemLevelPositions = array();
    protected $_templatePath       = array();

    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => false,
            'cache_tags'     => array(Mirasvit_Menu_Model_Menu::CACHE_TAG, Mirasvit_Menu_Model_Item::CACHE_TAG),
        ));
    }

    public function getCacheKeyInfo()
    {
        $shortCacheId = array(
            'MENU_MENU',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template'  => $this->getTemplate(),
            'name'      => $this->getNameInLayout(),
            'menu_code' => $this->getMenu() ? $this->getMenu()->getCode() : '',
            'position'  => $this->getPosition(),
            $this->getCurrentItemPath()
        );
        $cacheId = $shortCacheId;

        $shortCacheId = array_values($shortCacheId);
        $shortCacheId = implode('|', $shortCacheId);
        $shortCacheId = md5($shortCacheId);

        $cacheId['item_path'] = $this->getCurrentItemPath();
        $cacheId['short_cache_id'] = $shortCacheId;

        return $cacheId;
    }

    public function getCurrentItemPath()
    {
        Varien_Profiler::start('menu_getCurrentItemPath');
        if ($this->_currentItemPath === null) {
            $this->_currentItemPath = false;
            if ($this->getMenu()) {
                $collection = Mage::getModel('menu/item')->getCollection()
                    ->addFieldToFilter('menu_id', $this->getMenu()->getId())
                    ->addStoreFilter(Mage::app()->getStore()->getId());

                foreach ($collection as $item) {
                    if ($item->getTypeInstance()->isActive()) {
                        $this->_currentItemPath = $item->getPath();
                        break;
                    }
                }
            }
        }
        Varien_Profiler::stop('menu_getCurrentItemPath');
        return $this->_currentItemPath;
    }

    public function setCurrentItemPath($path)
    {
        $this->_currentItemPath = $path;

        return $this;
    }

    public function isItemActive($item)
    {
        if ($this->getCurrentItemPath()) {
            return in_array($item->getId(), explode('/', $this->getCurrentItemPath()));
        }

        return false;
    }

    protected function _getItemPosition($level)
    {
        Varien_Profiler::start('menu_getItemPosition');
        if ($level == 0) {
            $zeroLevelPosition = isset($this->_itemLevelPositions[$level]) ? $this->_itemLevelPositions[$level] + 1 : 1;
            $this->_itemLevelPositions = array();
            $this->_itemLevelPositions[$level] = $zeroLevelPosition;
        } elseif (isset($this->_itemLevelPositions[$level])) {
            $this->_itemLevelPositions[$level]++;
        } else {
            $this->_itemLevelPositions[$level] = 1;
        }

        $position = array();
        for($i = 0; $i <= $level; $i++) {
            if (isset($this->_itemLevelPositions[$i])) {
                $position[] = $this->_itemLevelPositions[$i];
            }
        }
        Varien_Profiler::stop('menu_getItemPosition');
        return implode('-', $position);
    }

    public function _renderItemHtml($item, $isFirst = false, $isLast = false)
    {
        Varien_Profiler::start('menu_renderItemHtml');
        Varien_Profiler::start('menu_renderItemHtml1');
        if (!$item->getIsActive()) {
            return;
        }

        $isActive = $this->isItemActive($item);
        $block    = $this->_getItemBlock($item);
        Varien_Profiler::stop('menu_renderItemHtml1');
        Varien_Profiler::start('menu_renderItemHtml2');
        $item->setBlock($block);
        $block->setItem($item)
            ->setNav('nav-'.$this->_getItemPosition($item->getLevel()))
            ->setIsActive($isActive)
            ->setIsFirst($isFirst)
            ->setIsLast($isLast);
        Varien_Profiler::stop('menu_renderItemHtml2');
        Varien_Profiler::start('menu_renderItemHtml3');
        $childHtml = '';
        $children = array();
        if (isset($this->items[$item->getId()])) {
            $children = $this->items[$item->getId()];
        }
        $cnt       = count($children);
        $i         = 0;
        Varien_Profiler::stop('menu_renderItemHtml3');
        Varien_Profiler::stop('menu_renderItemHtml1');

        foreach ($children as $child) {
            $child->setParent($item);
            $childHtml .= $this->_renderItemHtml($child, $i == 0, $i == $cnt - 1);
            $i++;
        }

        $block->setChildrenHtml($childHtml);
        Varien_Profiler::stop('menu_renderItemHtml');
        return $block->toHtml();
    }    

    public function _renderLinkHtml($link, $isFirst = false, $isLast = false)
    {
        $block = $this->_getItemBlock($link);
        $block->setItem($link)
            ->setIsFirst($isFirst)
            ->setIsLast($isLast);

        $block->setChildrenHtml('');

        return $block->toHtml();
    }

    protected function _getItemBlock($item)
    {        
        Varien_Profiler::start('menu_getItemBlock');
        $block = Mage::app()->getLayout()->createBlock('core/template');

        $level = $item->getLevel();
        $type  = $item->getType();

        if (!$type) {
            $type = 'simple_link';
        }
        $block->setTemplate($this->getTemplatePath($level, $type));
        Varien_Profiler::stop('menu_getItemBlock');
        return $block;
    }

    protected function getTemplatePath($level, $type)
    {
        $key = $level.'_'.$type;
        
        if (!isset($this->_templatePath[$key])) {
            $path           = false;
            $skinLevelTpl   = 'menu/'.$this->getMenu()->getTemplate().'/level_'.$level.'/'.$type.'.phtml';
            $skinDefaultTpl = 'menu/'.$this->getMenu()->getTemplate().'/default/'.$type.'.phtml';
            $levelTpl       = 'menu/default/level_'.$level.'/'.$type.'.phtml';
            $defaultTpl     = 'menu/default/default/'.$type.'.phtml';        
            
            if (file_exists(Mage::getDesign()->getTemplateFilename($skinLevelTpl))) {
                $path = $skinLevelTpl;
            } elseif (file_exists(Mage::getDesign()->getTemplateFilename($skinDefaultTpl))) {
                $path = $skinDefaultTpl;
            } elseif (file_exists(Mage::getDesign()->getTemplateFilename($levelTpl))) {
                $path = $levelTpl;
            } else {
                $path = $defaultTpl;
            }

            $this->_templatePath[$key] = $path;
        }

        return $this->_templatePath[$key];
    }

    public function renderMenuHtml()
    {
        $html         = '';

        $items = Mage::getModel('menu/item')->getCollection()
            ->addFieldToFilter('menu_id', $this->getMenu()->getId())        
            ->setOrder('position', 'asc')
            ->addStoreFilter(Mage::app()->getStore()->getId());

        $itemsArray = array();
        foreach ($items as $item) {
            $parentId = $item->getParentId();
            if ($parentId == null) {
                $parentId  = 'root';
            }

            if (!isset($itemsArray[$parentId])) {
                $itemsArray[$parentId] = array();
            }
            $itemsArray[$parentId][] = $item;
        }
        $this->items = $itemsArray;

        if (isset($this->items['root'])) {          
            $links = $this->getLinks();
            $cnt = count($this->items['root']) + count($links);
            $i = 0;

            foreach ($links as $link) {
                $html .= $this->_renderLinkHtml($link, $i == 0, $i == $cnt - 1);
                $i++;
            }

            foreach ($this->items['root'] as $item) {
                $html .= $this->_renderItemHtml($item, $i == 0, $i == $cnt - 1);
                $i++;
            }
        }

        return $html;
    }

    public function getMenuClass()
    {
        $class = array();
        $class[] = 'store-'.Mage::app()->getStore()->getId();
        $class[] = 'menu-'.$this->getMenu()->getCode();

        return implode(' ', $class);
    }

    public function placeMenu($position, $name)
    {
        $collection = Mage::getModel('menu/menu')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addFieldToFilter('position', $position);

        if ($collection->count() > 0) {
            $menu = $collection->getFirstItem();
            $this->setNameInLayout($name)
                ->setPosition($position)
                ->setMenu($menu)
                ->setBlockAlias($name)
                ->setTemplate($menu->getTemplatePath());
        }

        return $this;
    }

    public function setMenuCode($code)
    {
        $collection = Mage::getModel('menu/menu')->getCollection()
            ->addFieldToFilter('code', $code);

        if ($collection->count() > 0) {
            $menu = $collection->getFirstItem();
            $this->setMenu($menu)
                ->setTemplate($menu->getTemplatePath());
        }

        return $this;
    }

    public function setMenuTemplate($template)
    {
        if($this->getMenu()) {
            $this->getMenu()->setTemplate($template);
        }

        return $this;
    }
}