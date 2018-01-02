<?php

interface ThenHook {

    static public function getName();

    public function getEditTemplate(Hook $hook, $attributes);

    public function perform(Hook $hook, $parameters);
}