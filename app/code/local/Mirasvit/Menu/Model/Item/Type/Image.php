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


class Mirasvit_Menu_Model_Item_Type_Image extends Mirasvit_Menu_Model_Item_Type_Link
{
    protected $_styleAttributes = array(
        'top'    => 'top',
        'bottom' => 'bottom',
        'left'   => 'left',
        'right'  => 'right',
    );

    public function beforeSave($object)
    {
        $this->_saveImages($object);

        return $this;
    }

    public function getImageUrl()
    {
        $image = $this->getItem()->getAttr('image');
        $url = Mage::getBaseUrl('media').'menu'.DS.$image;

        return $url;
    }

    public function getStyle()
    {
        $styles = parent::getStyle();
        if ($styles) {
            $styles .= 'position:absolute;';
        }

        return $styles;
    }

    public function _saveImages($object)
    {
        $path = Mage::getBaseDir('media').DS.'menu'.DS;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if (is_array($object->getAttr('image'))) {
            $image = $object->getAttr('image');
            $image['value'] = substr($image['value'], strlen('menu/'));
            $object->setAttr($image['value'], 'image');
        }
        // pr($object);die();
        try {
            $uploader = new Mage_Core_Model_File_Uploader('attr[image]');
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);

            $result = $uploader->save($path);
            $object->setAttr($result['file'], 'image');
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }
}