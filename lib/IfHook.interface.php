<?php

interface IfHook {

    /**
     * Return the name of the IfHook.
     * @return string : a short name
     */
    static public function getName();

    public function getParameters();

    /**
     * Returns an array of NotificationCenter events like 'CourseDidStore'. Return empty array if you don't want to
     * listen to NotificationCenter.
     * @return array
     */
    public function listenToNotificationEvents();

    /**
     * We hand over the object (possibly a SORM) which we got from the NotificationCenter (if you dont listen
     * to NotificationCenter, then ignore this method) and want to get all relevant Hook-objects.
     * @param mixed $object : the $object we get from NotificationCenter
     * @return array of Hook-objects
     */
    public function findHooksByObject($object);

    /**
     * Returns a Flexi_Template for editing a Hook. The Hook object is given, although it might even be a new Hook
     * object. Use the value of the Hook object to display the default values of your form.
     * @param Hook $hook
     * @return Flexi_Template
     */
    public function getEditTemplate(Hook $hook);

    /**
     * Gets a hook, checks if it is responsible and returns an associative array with values to the parameters.
     * These parameters will be given to the ThenHook afterwards.
     * @param Hook $hook
     * @param $type
     * @param $event
     * @param $object
     * @return mixed
     */
    public function check(Hook $hook, $type, $event, $object);

}