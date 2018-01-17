<?php

class IfActivityHook implements IfHook {

    static public function getName()
    {
        return _("Aktivitäten");
    }

    public function getParameters(Hook $hook)
    {
        return array("text", "url", "verb", "user_id", "name");
    }

    public function getEditTemplate(Hook $hook)
    {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("ifs/activity/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function listenToNotificationEvents()
    {
        return array("Studip\\Activity\\ActivityDidCreate");
    }

    public function findHooksByObject($object)
    {
        switch ($object['context']) {
            case "system":
                return Hook::findBySQL("if_type = ? 
                    AND activated = '1'", array(
                    get_class($this)
                ));
            case "course":
                return Hook::findBySQL("INNER JOIN seminar_user USING (user_id) 
                    WHERE if_type = ? 
                    AND activated = '1'
                    AND seminar_user.Seminar_id = ?", array(
                    get_class($this),
                    $object['context_id']
                ));
            case "user":
                return Hook::findBySQL("if_type = ? 
                    AND activated = '1'
                    AND user_id = ?", array(
                    get_class($this),
                    $object['context_id']
                ));
        }

    }

    public function check(Hook $hook, $type, $event, $activity)
    {
        if (($hook['user_id'] !== $activity['actor_id']) || !$hook['if_settings']['notmine']) {
            $activity_array = $activity->toArray();
            $url = "";
            foreach ($activity['object_url'] as $u => $d) {
                $url = $u;
                break;
            }
            return array(
                'text' => $activity_array["content"],
                'verb' => $activity["verb"],
                'user_id' => $activity["actor_id"],
                'name' => User::find($activity["actor_id"])->getFullName(),
                'url' => $url
            );
        } else {
            return false;
        }
    }
}