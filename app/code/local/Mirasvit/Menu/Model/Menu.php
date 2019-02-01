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


class Mirasvit_Menu_Model_Menu extends Mage_Core_Model_Abstract
{
    const ENTITY    = 'MENU';
    const CACHE_TAG = 'MENU';

    protected $_cacheTag =  self::CACHE_TAG;

    protected function _construct()
    {
        $this->_init('menu/menu');
    }

    public function getChildren($store = null)
    {
        $collection = Mage::getModel('menu/item')->getCollection()
            ->addFieldToFilter('menu_id', $this->getId())
            ->addFieldToFilter('parent_id', array('null' => 1))
            ->addFieldToFilter('level', 1)
            ->setOrder('position', 'asc');

        if ($store) {
            $collection->addStoreFilter($store);
        }

        return $collection;
    }

    public function getChildrenCount()
    {
        return $this->getChildren()->count();
    }

    public function hasChildren()
    {
        return $this->getChildrenCount() > 0 ? 1 : 0;
    }

    public function getTemplatePath()
    {
        if (!$this->getTemplate()) {
            $this->setTemplate('default');
        }

        if ($this->getTemplate()) {
            return 'menu/'.$this->getTemplate().'/menu.phtml';
        }
    }
}