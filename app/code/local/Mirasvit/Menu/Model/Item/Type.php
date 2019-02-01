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


class Mirasvit_Menu_Model_Item_Type
{
    const TYPE_LINK    = 'link';
    const TYPE_COLUMN  = 'column';
    const TYPE_ROW     = 'row';
    const TYPE_DIVIDER = 'divider';
    const TYPE_HEADER  = 'header';
    const TYPE_IMAGE   = 'image';
    const TYPE_TEXT    = 'text';

    public static function factory($item)
    {
        $types  = self::getTypes();
        $type = $item->getType();

        $typeModelName = 'menu/item_type_'.$type;

        $typeModel = Mage::getModel($typeModelName);
        $typeModel->setItem($item);

        return $typeModel;
    }

    static public function getOptionArray()
    {
        $options = self::getTypes();

        return $options;
    }

    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value'=>'', 'label'=>'');
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    static public function getOptions()
    {
        $res = array();
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    static public function getTypes()
    {
        return array (
            self::TYPE_LINK    => Mage::helper('menu')->__('Link'),
            self::TYPE_COLUMN  => Mage::helper('menu')->__('Column'),
            self::TYPE_ROW     => Mage::helper('menu')->__('Row'),
            self::TYPE_DIVIDER => Mage::helper('menu')->__('Separator'),
            self::TYPE_HEADER  => Mage::helper('menu')->__('Header'),
            self::TYPE_IMAGE   => Mage::helper('menu')->__('Image'),
            self::TYPE_TEXT    => Mage::helper('menu')->__('Text'),
        );
    }
}
