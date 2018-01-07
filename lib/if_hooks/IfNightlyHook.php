<?php

class IfNightlyHook implements IfHook {

    static public function getName()
    {
        return _("Einmal in der Nacht");
    }

    public function getParameters(Hook $hook)
    {
        return array();
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
        $template = $tf->open("ifs/nightly/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function check(Hook $hook, $type, $event, $request)
    {
        return array();
    }
}