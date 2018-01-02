<?php

interface IfHook {

    static public function getName();

    public function getParameters();

    public function listenToNotificationEvents();

    /**
     * Return the field-name of the corresponding object. Only needed if IfHook listens to NotificationCenter. If it returns NULL this means the object listens to any notifications.
     * @return null|string
     */
    public function userIdField();

    public function getEditTemplate(Hook $hook);

    public function check(Hook $hook, $type, $event, $object);

}