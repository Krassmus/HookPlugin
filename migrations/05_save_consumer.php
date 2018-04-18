<?php

class SaveConsumer extends Migration
{
    function up()
    {
        DBManager::get()->exec("
            ALTER TABLE `hooks` 
            ADD COLUMN `consumer_id` CHAR(32) NULL AFTER `editable`;
        ");
        SimpleORMap::expireTableScheme();
    }
    
    function down()
    {
        DBManager::get()->exec("
            ALTER TABLE `hooks` 
            DROP COLUMN `consumer_id`;
        ");
        SimpleORMap::expireTableScheme();
    }
    
    
}