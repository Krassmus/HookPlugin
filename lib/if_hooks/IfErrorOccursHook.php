<?php

class IfErrorOccursHook implements IfHook {

    static public function getName()
    {
        return _("Fehler eines Hooks");
    }

    public function getParameters(Hook $hook)
    {
        return array("message", "hookname", "if_type", "then_type");
    }

    public function getEditTemplate(Hook $hook)
    {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("ifs/erroroccurs/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function listenToNotificationEvents()
    {
        return array("HookLogDidCreate");
    }

    public function findHooksByObject($object)
    {
        $object->hook->user_id;
        return Hook::findBySQL("user_id = ? AND if_type = '' ", array(
            $object->hook->user_id,
            get_class($this)
        ));
    }

    public function check(Hook $hook, $type, $event, $notification_user)
    {
        $latest_log = HookLog::findOneBySQL("hook_id = ? ORDER BY mkdate DESC LIMIT 1", array($hook->getId()));
        return array(
            'message' => $latest_log['log_text'],
            'hookname' => $hook['name'],
            'if_type' => $hook['if_type'],
            'then_type' => $hook['then_type']
        );
    }
}