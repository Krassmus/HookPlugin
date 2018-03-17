<?php

class AddNoneditableHooks extends Migration
{
    function up()
    {
        DBManager::get()->exec("
            ALTER TABLE `hooks` 
            ADD COLUMN `editable` TINYINT(4) DEFAULT '1' AFTER `last_triggered`;
        ");
        SimpleORMap::expireTableScheme();
    }
    
    function down()
    {
        DBManager::get()->exec("
            ALTER TABLE `hooks` 
            DROP COLUMN IF EXISTS `editable`;
        ");
        SimpleORMap::expireTableScheme();
    }
    
    
}