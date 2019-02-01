<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('padoo_testimonial'), 'store_id',
    'varchar(255) NOT NULL  AFTER testimonial_sidebar'
);

$installer->endSetup();