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


class Mirasvit_Menu_Block_Adminhtml_Menu_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $model = Mage::registry('current_menu');

        if (!$model) {
            $model = Mage::getModel('menu/menu');
        }

        $set = $form->addFieldset('menu_form', array('legend' => Mage::helper('menu')->__('General Information')));

        $set->addField('name', 'text', array(
            'label'    => Mage::helper('menu')->__('Name'),
            'required' => true,
            'name'     => 'name',
            'value'    => $model->getName()
        ));

        $set->addField('code', 'text', array(
            'label'    => Mage::helper('menu')->__('Code'),
            'required' => true,
            'name'     => 'code',
            'value'    => $model->getCode(),
        ));

        $set->addField('position', 'select', array(
            'label'    => Mage::helper('menu')->__('Display Position'),
            'required' => true,
            'name'     => 'position',
            'value'    => $model->getPosition(),
            'values'   => Mage::getSingleton('menu/system_config_source_position')->toOptionArray(),
        ));

        $set->addField('template', 'select', array(
            'label'    => Mage::helper('menu')->__('Template'),
            'required' => true,
            'name'     => 'template',
            'value'    => $model->getTemplate(),
            'values'   => Mage::getSingleton('menu/system_config_source_template')->toOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $set->addField('store_id', 'multiselect', array(
                'name'     => 'store_ids[]',
                'label'    => Mage::helper('menu')->__('Visible in Store View'),
                'title'    => Mage::helper('menu')->__('Visible in Store View'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value'    => $model->getStoreIds()
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);

            $field = $set->addField('sync_store', 'select', array(
                'name'     => 'sync_store',
                'label'    => Mage::helper('menu')->__('Apply same store views to childs'),
                'title'    => Mage::helper('menu')->__('Apply same store views to childs'),
                'required' => false,
                'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
                'value'    => 0
            ));
        }
        else {
            $set->addField('store_id', 'hidden', array(
                'name'  => 'store_ids[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        return parent::_prepareForm();
    }
}

