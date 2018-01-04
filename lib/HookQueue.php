<?php

class HookQueue extends SimpleORMap {

    protected static function configure($config = array())
    {
        $config['db_table'] = 'hooks_queue';
        $config['serialized_fields']['parameters'] = "JSONArrayObject";
        parent::configure($config);
    }

}