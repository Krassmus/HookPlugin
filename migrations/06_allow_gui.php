<?php

class AllowGui extends Migration
{
    function up() {
        Config::get()->create("HOOKS_ALLOW_GUI", array(
            'value' => 1,
            'type' => "boolean",
            'range' => "global",
            'section' => "HOOKPLUGIN"
        ));
    }
    
    function down() {
        Config::get()->delete("HOOKS_ALLOW_GUI");
    }
    
    
}