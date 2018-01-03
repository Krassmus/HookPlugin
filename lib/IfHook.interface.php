<?php

interface IfHook {

    static public function getName();

    public function getParameters();

    public function listenToNotificationEvents();

    public function findHooksByIftypeAndObject($type, $object);

    public function getEditTemplate(Hook $hook);

    public function check(Hook $hook, $type, $event, $object);

}