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




class Mirasvit_Menu_Model_Resource_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('menu/item');
    }

    public function addStoreFilter($store)
    {
        if (!Mage::app()->isSingleStoreMode()) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            $this->getSelect()
                ->joinLeft(array('store_table' => $this->getTable('menu/item_store')),
                        'main_table.item_id = store_table.item_id', array())
                ->where('store_table.store_id in (?)', array(0, $store));
            return $this;
        }
        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this->_items as $item) {
            $item->getResource()->_afterLoad($item);
        }

        return $this;
    }
}