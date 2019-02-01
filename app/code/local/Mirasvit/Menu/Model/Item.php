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


class Mirasvit_Menu_Model_Item extends Mage_Core_Model_Abstract
{
    const ENTITY    = 'MENU_ITEM';
    const CACHE_TAG = 'MENU_ITEM';

    protected $_cacheTag =  self::CACHE_TAG;

    protected function _construct()
    {
        $this->_init('menu/item');
    }

    /**
     * Get parent category identifier
     *
     * @return int
     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * Get all parent categories ids
     *
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    public function hasChildren()
    {
        return $this->getChildrenCount() > 0 ? 1 : 0;
    }

    public function getChildren($store = null)
    {
        $collection = Mage::getModel('menu/item')->getCollection()
            ->addFieldToFilter('parent_id', $this->getId())
            ->addFieldToFilter('level', $this->getLevel() + 1)
            ->setOrder('position', 'asc');

        if ($store) {
            $collection->addStoreFilter($store);
        }

        return $collection;
    }

    public function getChildrenByType($type)
    {
        $collection = $this->getChildren()
            ->addFieldToFilter('type', $type);

        return $collection;
    }

    public function loadPathArray($path)
    {
        $result = array();

        $ids = explode('/', $path);

        foreach ($ids as $itemId) {
            $result[] = Mage::getModel('menu/item')->load($itemId);
        }

        return $result;
    }

    public function move($parentId, $afterItemId)
    {
        if ($parentId != null) {
            $parent = Mage::getModel('menu/item')
                ->load($parentId);

            if (!$parent->getId()) {
                Mage::throwException(
                    Mage::helper('menu')->__('Item move operation is not possible: the new parent item was not found.')
                );
            }
        } else {
            $parent = Mage::getModel('menu/item');
        }

        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('menu')->__('Item move operation is not possible: the current item was not found.')
            );
        } elseif ($parent && $parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('menu')->__('Item move operation is not possible: parent item is equal to child item.')
            );
        }

        $this->setMovedItemId($this->getId());

        $this->getResource()->changeParent($this, $parent, $afterItemId);

        return $this;
    }

    public function getTypeInstance()
    {
        if ($this->_typeInstance === null) {
            $this->_typeInstance = Mage::getSingleton('menu/item_type')
                ->factory($this);
        }
        return $this->_typeInstance;
    }

    public function getAttr($attribute = null) {
        if ($attribute != null) {
            $attr = $this->getData('attr');
            if (isset($attr[$attribute])) {
                return $attr[$attribute];
            }

            return null;
        }

        return $this->getData('attr');
    }

    public function setAttr($value, $attribute = null) {
        if ($attribute != null) {
            $attr = $this->getData('attr');
            if (!is_array($attr)) {
                $attr = array();
            }
            $attr[$attribute] = $value;
            parent::setAttr($attr);
        } else {
            parent::setAttr($value);
        }

        return $this;
    }

    protected function _beforeSave()
    {
        $this->getTypeInstance()->beforeSave($this);

        parent::_beforeSave();
    }
}