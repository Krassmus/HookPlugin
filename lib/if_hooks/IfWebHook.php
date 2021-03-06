<?php

class IfWebHook implements IfHook {

    static public function getName()
    {
        return _("Webhook");
    }

    public function getParameters(Hook $hook)
    {

        return $hook['if_settings']['example'] ? array_keys($hook['if_settings']['example']) : array();
    }

    public function listenToNotificationEvents()
    {
        return array();
    }

    public function findHooksByObject($object)
    {
    }

    public function getEditTemplate(Hook $hook)
    {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("ifs/webhook/edit.php");
        $template->hook = $hook;
        $template->security_hash = self::getSecurityHash($hook->getId());
        return $template;
    }

    static public function getSecurityHash($hook_id)
    {
        return sha1($hook_id.Config::get()->STUDIP_INSTALLATION_ID);
    }

    public function check(Hook $hook, $type, $event, $request)
    {
        $hook['if_settings']['example'] = $request;
        $hook->store();
        return (array) $request;
    }
}