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




class Mirasvit_Menu_Model_Resource_Item extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('menu/item', 'item_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        if (is_array($object->getAttr())) {
            $object->setAttr(serialize($object->getAttr()));
        }

        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }

        if ($object->getLevel() === null) {
            $object->setLevel(1);
        }

        if (!$object->getId()) {
            $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            $path  = explode('/', $object->getPath());
            $level = count($path);
            $object->setLevel($level + 1);

            if ($object->getParentId() == null) {
                $object->setLevel(1);
            }

            $object->setPath($object->getPath() . '/');

            $toUpdateChild = explode('/', $object->getPath());

            $this->_getWriteAdapter()->update(
                $this->getTable('item'),
                array('children_count'  => new Zend_Db_Expr('children_count + 1')),
                array('item_id IN(?)' => $toUpdateChild)
            );
        }

        if ($object->getParentId() == 0) {
            $object->setParentId(null);
        }

        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassStatus()) {
            if ($object->getPath() ==  '/') {
                $object->setPath($object->getId());
                $this->_savePath($object);
            } elseif (substr($object->getPath(), -1) == '/') {
                $object->setPath($object->getPath() . $object->getId());
                $this->_savePath($object);
            }

            $this->_saveStore($object);
        }

        return parent::_afterSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        foreach ($object->getChildren() as $children) {
            $children->delete();
        }

        return parent::_beforeDelete($object);
    }

    public function getChildrenCount($itemId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('item'), 'children_count')
            ->where('item_id = :item_id');
        $bind = array('item_id' => $itemId);

        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    public function changeParent($item, $newParent, $afterItemId = null)
    {
        $childrenCount = $this->getChildrenCount($item->getId()) + 1;
        $table         = $this->getTable('item');
        $adapter       = $this->_getWriteAdapter();
        $levelFiled    = $adapter->quoteIdentifier('level');
        $pathField     = $adapter->quoteIdentifier('path');

        /**
         * Decrease children count for all old Item parent categories
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count - '.$childrenCount)),
            array('item_id IN(?)' => $item->getParentIds())
        );

        /**
         * Increase children count for new Item parents
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count + '.$childrenCount)),
            array('item_id IN(?)' => $newParent->getPathIds())
        );

        $position = $this->_processPositions($item, $newParent, $afterItemId);

        if ($newParent->getPath()) {
            $newPath = sprintf('%s/%s', $newParent->getPath(), $item->getId());
        } else {
            $newPath = $item->getId();
        }
        $newLevel         = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $item->getLevel();

        /**
         * Update children nodes path
         */
        $adapter->update(
            $table,
            array(
                'path' => new Zend_Db_Expr('REPLACE('.$pathField.','.$adapter->quote($item->getPath().'/').', '.$adapter->quote($newPath.'/').')'),
                'level' => new Zend_Db_Expr($levelFiled.' + '.$levelDisposition)
            ),
            array($pathField.' LIKE ?' => $item->getPath().'/%')
        );

        /**
         * Update moved item data
         */
        $data = array(
            'path'      => $newPath,
            'level'     => $newLevel,
            'position'  => $position,
            'parent_id' => $newParent->getId()
        );
        $adapter->update($table, $data, array('item_id = ?' => $item->getId()));

        $item->addData($data);

        return $this;
    }

    public function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassDelete()) {
            $object->setAttr(@unserialize($object->getAttr()));
            $this->_loadStore($object);
        }

        return parent::_afterLoad($object);
    }
    
    protected function _savePath($object)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getTable('item'),
                array('path' => $object->getPath()),
                array('item_id = ?' => $object->getId())
            );
        }
        return $this;
    }

    protected function _saveStore(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getWriteAdapter();

        $condition = $adapter->quoteInto('item_id = ?', $object->getId());
        $adapter->delete($this->getTable('menu/item_store'), $condition);

        foreach ((array)$object->getData('store_ids') as $store) {
            $insert = array(
                'item_id'  => $object->getId(),
                'store_id' => $store
            );
            $adapter->insert($this->getTable('menu/item_store'), $insert);
        }
    }

    protected function _loadStore(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('menu/item_store'))
            ->where('item_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['store_id'];
            }
            $object->setData('store_ids', $array);
        }
        return $object;
    }

    protected function _processPositions($item, $newParent, $afterItemId)
    {
        $table         = $this->getTable('item');
        $adapter       = $this->_getWriteAdapter();
        $positionField = $adapter->quoteIdentifier('position');

        $bind = array(
            'position' => new Zend_Db_Expr($positionField . ' - 1')
        );
        $where = array(
            'parent_id = ?'       => $item->getParentId(),
            $positionField.' > ?' => $item->getPosition()
        );
        $adapter->update($table, $bind, $where);

        /**
         * Prepare position value
         */

        if ($afterItemId) {
            $select = $adapter->select()
                ->from($table,'position')
                ->where('item_id = :item_id');
            $position = $adapter->fetchOne($select, array('item_id' => $afterItemId));

            $bind = array(
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            );

            if (intval($newParent->getId()) == 0) {
                $where = array(
                    'parent_id IS NULL',
                    $positionField.' > ?' => $position
                );
            } else {
                $where = array(
                    'parent_id = ?' => $newParent->getId(),
                    $positionField.' > ?' => $position
                );
            }

            $adapter->update($table, $bind, $where);
        } elseif ($afterItemId !== null) {
            $position = 0;
            $bind = array(
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            );

            if (intval($newParent->getId()) == 0) {
                $where = array(
                    'parent_id IS NULL',
                    $positionField.' > ?' => $position
                );
            } else {
                $where = array(
                    'parent_id = ?' => $newParent->getId(),
                    $positionField.' > ?' => $position
                );
            }

            $adapter->update($table,$bind,$where);
        } else {
            $select = $adapter->select()
                ->from($table,array('position' => new Zend_Db_Expr('MIN(' . $positionField. ')')))
                ->where('parent_id = :parent_id');
            $position = $adapter->fetchOne($select, array('parent_id' => $newParent->getId()));
        }
        $position += 1;

        return $position;
    }

    protected function _getMaxPosition($path)
    {
        $adapter       = $this->getReadConnection();
        $positionField = $adapter->quoteIdentifier('position');
        $level         = count(explode('/', $path));
        if ($path == '') {
            $level = 1;
            $path = '%';
        } else {
            $level++;
            $path .= '/%';
        }
        $bind = array(
            'c_level' => $level,
            'c_path'  => $path
        );
        $select  = $adapter->select()
            ->from($this->getTable('item'), 'MAX(' . $positionField . ')')
            ->where($adapter->quoteIdentifier('path') . ' LIKE :c_path')
            ->where($adapter->quoteIdentifier('level') . ' = :c_level');
        $position = $adapter->fetchOne($select, $bind);

        if (!$position) {
            $position = 0;
        }
        return $position;
    }

}