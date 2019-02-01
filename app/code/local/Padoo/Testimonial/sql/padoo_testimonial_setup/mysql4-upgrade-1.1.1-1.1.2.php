<?php

/**
 * Testimonial - Magento Extension
 *
 * @package Testimonial
 * @category Padoo
 * @copyright Copyright 2015 Padoo. 
 */
 
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$configValuesMap = array(
  'padoo_testimonial/options/mail_to_store_template' => 'padoo_testimonial_options_mail_to_store_template',
);

foreach ($configValuesMap as $configPath=>$configValue) {
    $installer->setConfigData($configPath, $configValue);
}

