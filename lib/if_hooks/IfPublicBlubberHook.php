<?php

class IfPublicBlubberHook implements IfHook {

    static public function getName()
    {
        return _("Öffentlicher Blubber");
    }

    public function getParameters(Hook $hook)
    {
        return array("von_id", "von_name", "nachricht");
    }

    public function listenToNotificationEvents()
    {
        return array("BlubberPostingDidCreate");
    }

    public function findHooksByObject($object)
    {
        if (($object['context_type'] === "public") && !$object['parent_id']) {
            return Hook::findBySQL("if_type = ? AND activated = '1' ", array(get_class($this)));
        } else {
            return array();
        }
    }

    public function getEditTemplate(Hook $hook)
    {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("ifs/publicblubber/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function check(Hook $hook, $type, $event, $blubber)
    {
        if (!$hook['if_settings']['onlymine'] || ($hook['user_id'] === $blubber['user_id'])) {
            return array(
                'von_id' => $blubber['user_id'],
                'von_name' => User::find($blubber['user_id'])->getFullName(),
                'nachricht' => $blubber['description']
            );
        } else {
            return false;
        }
    }
}