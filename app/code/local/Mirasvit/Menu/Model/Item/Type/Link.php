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


class Mirasvit_Menu_Model_Item_Type_Link extends Mirasvit_Menu_Model_Item_Type_Abstract
{
    const LINK_TYPE_EMPTY    = 'empty';
    const LINK_TYPE_CATEGORY = 'category';
    const LINK_TYPE_CMS      = 'cms';
    const LINK_TYPE_PRODUCT  = 'product';
    const LINK_TYPE_DIRECT   = 'direct';

    const LINK_TARGET_SAME   = 'same';
    const LINK_TARGET_BLANK  = 'blank';

    protected $_styleAttributes = array(
        'fontweight' => 'font-weight',
        'fontsize'   => 'font-size',
        'color'      => 'color',
        'background' => 'background',
    );

    protected $_classAttributes = array(
        'css_class',
        'headerstyle',
    );

    public function toLinkTypeOptionArray()
    {
        return array (
            self::LINK_TYPE_EMPTY    => Mage::helper('menu')->__('Empty link'),
            self::LINK_TYPE_CATEGORY => Mage::helper('menu')->__('Category'),
            self::LINK_TYPE_PRODUCT  => Mage::helper('menu')->__('Product'),
            self::LINK_TYPE_CMS      => Mage::helper('menu')->__('Cms Page'),
            self::LINK_TYPE_DIRECT   => Mage::helper('menu')->__('Direct Url'),
        );
    }

    public function getUrl()
    {
        $url = '';
        switch ($this->getLinkTo()) {
            case self::LINK_TYPE_CATEGORY:
                $id  = $this->getItem()->getAttr('link_to_category');
                $url = Mage::getModel('catalog/category')->load($id)->getUrl();
            break;

            case self::LINK_TYPE_CMS:
                $id  = $this->getItem()->getAttr('link_to_cms');
                $url = Mage::helper("cms/page")->getPageUrl($id);
            break;

            case self::LINK_TYPE_DIRECT:
                $url = $this->getItem()->getAttr('link_to_direct');
            break;

            case self::LINK_TYPE_PRODUCT:
                $sku     = $this->getItem()->getAttr('link_to_product');
                $product = Mage::getModel('catalog/product');
                $id      = $product->getIdBySku($sku);
                $product->load($id);
                $url = $product->getProductUrl();
            break;
        }

        return $url;
    }

    public function getLinkTo()
    {
        return $this->getItem()->getAttr('link_to');
    }

    public function getNote()
    {
        return $this->getItem()->getAttr('note');
    }

    public function isActive()
    {
        switch ($this->getLinkTo()) {
            case self::LINK_TYPE_CATEGORY:
                $id  = $this->getItem()->getAttr('link_to_category');

                if (Mage::registry('current_category') && Mage::registry('current_category')->getId() == $id) {
                    return true;
                }
            break;

            case self::LINK_TYPE_DIRECT:
            case self::LINK_TYPE_CMS:
                if ($this->getUrl() == Mage::helper('core/url')->getCurrentUrl()) {
                    return true;
                }
            break;
        }

        return false;
    }
}