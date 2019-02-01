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


class Mirasvit_Menu_Model_Cache_Menu extends Enterprise_PageCache_Model_Container_Abstract
{
    protected function _getBlockCacheId()
    {
        return md5($this->_placeholder->getAttribute('short_cache_id'));
    }

    protected function _getItemCacheId()
    {
        $shortCacheId = $this->_placeholder->getAttribute('short_cache_id');
        $itemPath = $this->_placeholder->getAttribute('item_path');
        if (!$shortCacheId || !$itemPath) {
            return false;
        }
        return $shortCacheId . '_' . $itemPath;
    }

    public function applyWithoutApp(&$content)
    {
        $blockCacheId = $this->_getBlockCacheId();
        $itemCacheId = $this->_getItemCacheId();
        if ($blockCacheId && $itemCacheId) {
            $blockContent = $this->_loadCache($blockCacheId);
            $categoryUniqueClasses = $this->_loadCache($itemCacheId);
            if ($blockContent && $categoryUniqueClasses !== false) {
                if ($categoryUniqueClasses != '') {
                    $regexp = '';
                    foreach (explode(' ', $categoryUniqueClasses) as $categoryUniqueClass) {
                        $regexp .= ($regexp ? '|' : '') . preg_quote($categoryUniqueClass);
                    }
                    $blockContent = preg_replace('/(?<=\s|")(' . $regexp . ')(?=\s|")/u', '$1 active', $blockContent);
                }
                $this->_applyToContent($content, $blockContent);
                return true;
            }
        }
        return false;
    }

    public function saveCache($blockContent)
    {
        $cacheId = $this->_getBlockCacheId();
        if ($cacheId) {
            $itemCacheId = $this->_getItemCacheId();
            if ($itemCacheId) {
                $itemUniqueClasses = '';
                $classes = array();

                $classesCount = preg_match_all('/(?<=\s)class="(.*?active.*?)"/u', $blockContent, $classes);
                for ($i = 0; $i < $classesCount; $i++) {
                    $classAttribute = $classes[0][$i];
                    $classValue     = $classes[1][$i];
                    $classInactive  = preg_replace('/\s+active|active\s+|active/', '', $classAttribute);
                    $blockContent   = str_replace($classAttribute, $classInactive, $blockContent);
                    $matches        = array();
                    if (preg_match('/(?<=\s|^)nav-.+?(?=\s|$)/', $classValue, $matches)) {
                        $itemUniqueClasses .= ($itemUniqueClasses ? ' ' : '') . $matches[0];
                    }
                }

                $this->_saveCache($itemUniqueClasses, $itemCacheId, array(Mirasvit_Menu_Model_Menu::CACHE_TAG, Mirasvit_Menu_Model_Item::CACHE_TAG));
            }
            if (!Mage::app()->getCache()->test($cacheId)) {
                $this->_saveCache($blockContent, $cacheId, array(Mirasvit_Menu_Model_Menu::CACHE_TAG, Mirasvit_Menu_Model_Item::CACHE_TAG));
            }
        }
        return $this;
    }

    protected function _renderBlock()
    {

        $block    = $this->_placeholder->getAttribute('block');
        $template = $this->_placeholder->getAttribute('template');
        $code     = $this->_placeholder->getAttribute('menu_code');
        $position = $this->_placeholder->getAttribute('position');
        $itemPath = $this->_placeholder->getAttribute('item_path');

        $block = new $block;
        $block->setMenuCode($code);
        $block->setTemplate($template);
        $block->setPosition($position);
        $block->setCurrentItemPath($itemPath);

        return $block->toHtml();
    }
}
