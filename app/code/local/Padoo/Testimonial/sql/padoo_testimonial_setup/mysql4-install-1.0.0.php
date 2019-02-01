<?php
/**
 * Padoo Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0).
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you are unable to obtain it through the world-wide-web, please send
 * an email to info@padoo.com so we can send you a copy immediately.
 *
 * @category   Padoo
 * @package    Padoo_Testimonial
 * @copyright  Copyright (c) 2010-2012 Padoo Co. (http://padoo.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Installation script
 *
 * @category   Padoo
 * @package    Padoo_Testimonial
 * @author     Tien Nguyen <tiennd@padoosoft.com>
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getTable('padoo_testimonial');

$installer->run("
	DROP TABLE IF EXISTS {$table};
    CREATE TABLE IF NOT EXISTS {$table} (
        testimonial_id int(11) unsigned not null auto_increment,
        testimonial_position int(11) DEFAULT 0,
        testimonial_name varchar(50) NOT NULL DEFAULT '',
        testimonial_email varchar(50) NOT NULL DEFAULT '',
		testimonial_status smallint(6) NOT NULL DEFAULT '2',
        testimonial_text TEXT not null,
        testimonial_img varchar(128) DEFAULT NULL,
        testimonial_sidebar tinyint(4) NOT NULL,
        testimonial_date DATETIME DEFAULT NULL,
        PRIMARY KEY(testimonial_id)
    ) engine=InnoDB default charset=utf8;
");

$installer->endSetup();
