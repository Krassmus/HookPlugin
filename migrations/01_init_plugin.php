<?php
/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class InitPlugin extends Migration
{
    function up() {
        DBManager::get()->exec("
            CREATE TABLE `hooks` (
                `hook_id` varchar(32) NOT NULL DEFAULT '',
                `name` varchar(128) NOT NULL DEFAULT '',
                `activated` tinyint(4) DEFAULT '1',
                `cronjob` tinyint(4) DEFAULT '1',
                `user_id` varchar(32) NOT NULL DEFAULT '',
                `if_type` varchar(64) DEFAULT NULL,
                `if_settings` text,
                `then_type` varchar(64) DEFAULT NULL,
                `then_settings` text,
                `general_settings` text,
                `last_triggered` int(11) DEFAULT NULL,
                `chdate` int(11) NOT NULL,
                `mkdate` int(11) NOT NULL,
                PRIMARY KEY (`hook_id`)
            ) ENGINE=InnoDB;
        ");
        DBManager::get()->exec("
            CREATE TABLE `hooks_log` (
                `log_id` varchar(32) NOT NULL DEFAULT '',
                `hook_id` varchar(32) NOT NULL DEFAULT '',
                `log_text` text NOT NULL,
                `user_id` varchar(32) NOT NULL,
                `exception` tinyint(4) NOT NULL DEFAULT '0',
                `mkdate` int(11) NOT NULL,
                PRIMARY KEY (`log_id`)
            ) ENGINE=InnoDB;
        ");
        DBManager::get()->exec("
            CREATE TABLE `hooks_queue` (
                `hook_queue_id` varchar(32) NOT NULL DEFAULT '',
                `hook_id` varchar(32) DEFAULT NULL,
                `parameters` text,
                `user_id` varchar(32) DEFAULT NULL,
                `mkdate` int(11) DEFAULT NULL,
                PRIMARY KEY (`hook_queue_id`)
            ) ENGINE=InnoDB;
        ");
    }
    
    function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `hooks_queue` ");
        DBManager::get()->exec("DROP TABLE IF EXISTS `hooks_log` ");
        DBManager::get()->exec("DROP TABLE IF EXISTS `hooks` ");
    }
    
    
}