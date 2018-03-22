<?php

interface ThenHook {

    /**
     * Return the name of the ThenHook.
     * @return string : a short name
     */
    static public function getName();

    /**
     * Returns a Flexi_Template for editing a Hook. The Hook object is given, although it might even be a new Hook
     * object. Use the value of the Hook object to display the default values of your form.
     * @param Hook $hook
     * @return Flexi_Template
     */
    public function getEditTemplate(Hook $hook, $attributes);

    /**
     * This method is now the real Hook. It sends emails, pushes webhooks, writes something or fetches data from the
     * internet. Use the values of the parameters and the $hook['then_settings'] to fully customize the action of the ThenHook.
     * @param Hook $hook
     * @param array $parameters
     * @return null|string : write something to the log. Do this if performing was successful. If it was unsuccessful it is recommended to throw an Exception with fitting message.
     */
    public function perform(Hook $hook, $parameters, $multicurl = null);
}