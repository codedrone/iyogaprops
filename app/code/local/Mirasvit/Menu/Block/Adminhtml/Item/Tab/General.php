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


class Mirasvit_Menu_Block_Adminhtml_Item_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_item');
        $menu  = Mage::registry('current_menu');

        $form = new Varien_Data_Form();
        $form->setDataObject($model);
        $this->setForm($form);

        $general = $form->addFieldset('menu_form', array('legend' => Mage::helper('menu')->__('General Information')));

        if (!$model->getId()) {
            $parentId = $this->getRequest()->getParam('parent_id');
            if (!$parentId) {
                $parentId = null;
            }
            $general->addField('path', 'hidden', array(
                'name'  => 'path',
                'value' => $parentId
            ));
        } else {
            $general->addField('id', 'hidden', array(
                'name'  => 'id',
                'value' => $model->getId()
            ));
            $general->addField('path', 'hidden', array(
                'name'  => 'path',
                'value' => $model->getPath()
            ));
        }

        $general->addField('type', 'select', array(
            'label'    => Mage::helper('menu')->__('Item Type'),
            'name'     => 'type',
            'values'   => Mage::getSingleton('menu/item_type')->getOptionArray(),
            'value'    => $model->getType(),
            'required' => true,
            'disabled' => $model->getId() ? true : false,
        ));

        $general->addField('is_active', 'select', array(
            'label'    => Mage::helper('menu')->__('Is Active'),
            'name'     => 'is_active',
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value'    => $model->getId() ? $model->getIsActive() : 1,
            'required' => true,
        ));

        $general->addField('name', 'text', array(
            'label'     => Mage::helper('menu')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
            'value'     => $model->getName()
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $general->addField('store_id', 'multiselect', array(
                'name'     => 'store_ids[]',
                'label'    => Mage::helper('menu')->__('Visible in Store View'),
                'title'    => Mage::helper('menu')->__('Visible in Store View'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value'    => ($model->getId()) ? $model->getStoreIds() : $menu->getStoreIds(),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $general->addField('store_id', 'hidden', array(
                'name'  => 'store_ids[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        if ($model->getId()) {
            $this->addFieldSetByType($form, $model);
        }

        return parent::_prepareForm();
    }

    protected function addFieldSetByType($form, $model)
    {
        switch ($model->getType()) {
            case Mirasvit_Menu_Model_Item_Type::TYPE_LINK:
                $this->addLinkFieldset($form, $model);
                break;
            case Mirasvit_Menu_Model_Item_Type::TYPE_IMAGE:
                $this->addImageFieldset($form, $model);
                $this->addLinkFieldset($form, $model);
                break;
            case Mirasvit_Menu_Model_Item_Type::TYPE_TEXT:
                $this->addTextFieldset($form, $model);
                break;
        }

        return $this;
    }

    protected function addLinkFieldset($form, $model)
    {
        $set = $form->addFieldset('linkset', array('legend' => Mage::helper('menu')->__('Link Information')));

        $set->addField('link_to', 'switchfield', array(
            'label'    => Mage::helper('menu')->__('Link To'),
            'name'     => 'attr[link_to]',
            'values'   => $model->getTypeInstance()->toLinkTypeOptionArray(),
            'value'    => $model->getAttr('link_to'),
            'required' => true,
        ));

        $set->addField('link_to_category', 'select', array(
            'label'    => Mage::helper('menu')->__('Link To Category'),
            'name'     => 'attr[link_to_category]',
            'values'   => Mage::getSingleton('menu/system_config_source_category')->toOptionArray(false),
            'value'    => $model->getAttr('link_to_category'),
        ));

        $set->addField('link_to_product', 'text', array(
            'label'    => Mage::helper('menu')->__('Link To Product'),
            'name'     => 'attr[link_to_product]',
            'value'    => $model->getAttr('link_to_product'),
            'note'     => Mage::helper('menu')->__('Product SKU'),
        ));

        $set->addField('link_to_cms', 'select', array(
            'label'    => Mage::helper('menu')->__('Link To Cms'),
            'name'     => 'attr[link_to_cms]',
            'values'   => Mage::getModel('adminhtml/system_config_source_cms_page')->toOptionArray(),
            'value'    => $model->getAttr('link_to_cms'),
        ));

        $set->addField('link_to_direct', 'text', array(
            'label'    => Mage::helper('menu')->__('Direct Link'),
            'name'     => 'attr[link_to_direct]',
            'value'    => $model->getAttr('link_to_direct'),
            'note'     => Mage::helper('menu')->__('e.g. /electronics/cell-phones.html or http://google.com/'),
        ));

        $set->addField('note', 'textarea', array(
            'label' => Mage::helper('menu')->__('Note'),
            'name'  => 'attr[note]',
            'value' => $model->getAttr('note'),
        ));
    }

    protected function addImageFieldset($form, $model)
    {
        $set = $form->addFieldset('imageset', array('legend' => Mage::helper('menu')->__('Image Information')));
        $set->addField('image', 'image', array(
            'label' => Mage::helper('menu')->__('Image'),
            'name'  => 'attr[image]',
            'value' => 'menu'.DS.$model->getAttr('image'),
        ));
    }

    protected function addTextFieldset($form, $model)
    {
        $set = $form->addFieldset('textset', array('legend' => Mage::helper('menu')->__('Information')));
        $set->addField('text', 'editor', array(
            'label'    => Mage::helper('menu')->__('Text'),
            'name'     => 'attr[text]',
            'value'    => $model->getAttr('text'),
            'wysiwyg'  => true,
            'config'   => Mage::getSingleton('menu/system_config_wysiwyg')->getConfig(),
            'style'    => 'height:15em',
            'required' => false,
        ));
    }
}

