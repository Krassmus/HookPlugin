<?php

class HookLog extends SimpleORMap {

    protected static function configure($config = array())
    {
        $config['db_table'] = 'hooks_log';
        $config['belongs_to']['hook'] = [
            'class_name'  => 'Hook',
            'foreign_key' => 'hook_id',
        ];
        parent::configure($config);
    }

    static public function cleanUpLog()
    {
        self::deleteBySQL("mkdate < UNIX_TIMESTAMP() - 86400 * 14");
    }

}