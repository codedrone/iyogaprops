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




$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('menu/menu')}`;
CREATE TABLE `{$this->getTable('menu/menu')}` (
    `menu_id`    int(11)      NOT NULL AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL DEFAULT '',
    `position`   varchar(255) NOT NULL DEFAULT '',
    `template`   varchar(255) NOT NULL DEFAULT '',
    `code`       varchar(255) NOT NULL DEFAULT '',
    `created_at` timestamp    NOT NULL DEFAULT '0000-00-00 00:00:00',  
    `updated_at` timestamp    NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Menu';
");

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('menu/item')}`;
CREATE TABLE `{$this->getTable('menu/item')}` (
    `item_id`        int(11)      NOT NULL AUTO_INCREMENT,
    `menu_id`        int(11)      NOT NULL DEFAULT '0',
    `parent_id`      int(11)      NULL,
    `position`       int(11)      NOT NULL DEFAULT '0',
    `path`           varchar(255) NOT NULL DEFAULT '',
    `level`          int(11)      NOT NULL DEFAULT '1',
    `children_count` int(11)      NOT NULL DEFAULT '0',
    `type`           varchar(255) NOT NULL DEFAULT '',
    `name`           varchar(255) NOT NULL DEFAULT '',
    `attr`           varchar(255) NOT NULL DEFAULT '',
    `created_at`     timestamp    NOT NULL DEFAULT '0000-00-00 00:00:00',  
    `updated_at`     timestamp    NOT NULL DEFAULT '0000-00-00 00:00:00', 
    `is_active`      int(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Menu Item';
");

$installer->endSetup();