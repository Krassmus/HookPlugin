<?php

class Hook extends SimpleORMap {

    protected static function configure($config = array())
    {
        $config['db_table'] = 'hooks';
        $config['serialized_fields']['if_settings']      = "JSONArrayObject";
        $config['serialized_fields']['then_settings']    = "JSONArrayObject";
        $config['serialized_fields']['general_settings'] = "JSONArrayObject";
        parent::configure($config);
    }

}