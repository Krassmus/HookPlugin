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
            case "institute":
                return Hook::findBySQL("INNER JOIN user_inst USING (user_id) 
                    WHERE if_type = ? 
                    AND activated = '1'
                    AND user_inst.Institut_id = ?", array(
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
        return array();
    }

    public function check(Hook $hook, $type, $event, $activity)
    {
        if (($hook['user_id'] !== $activity['actor_id']) || !$hook['if_settings']['notmine']) {
            $provider = new $activity['provider']();
            $allowed = $provider->getActivityDetails($activity);

            if ($allowed) {
                $url = "";
                foreach ($activity['object_url'] as $u => $d) {
                    $url = $u;
                    if ($url) {
                        break;
                    }
                }

                $title = '';
                $object_text = $provider::getLexicalField();
                if (in_array($activity->actor_id, array('____%system%____', 'system')) !== false) {
                    $actor = _('Stud.IP');
                } else {
                    $actor = get_fullname($activity->actor_id);
                }
                $contextclass = "Studip\\Activity\\".ucfirst($activity->context) . 'Context';
                $contextcontainerclass = ucfirst($activity->context);
                $context = $contextcontainerclass === "Studip\\Activity\\System"
                    ? new $contextclass($provider)
                    : new $contextclass(new $contextcontainerclass($activity->context_id), $provider);
                $context_name = $context->getContextFullname();
                switch ($activity->context) {
                    case 'course':
                        $title = $actor .' '
                            . sprintf($activity->verbToText(),
                                $object_text . sprintf(_(' im Kurs "%s"'), $context_name)
                            );
                        break;
                    case 'institute':
                        $title = $actor .' '
                            . sprintf($activity->verbToText(),
                                $object_text . sprintf(_(' in der Einrichtung "%s"'), $context_name)
                            );
                        break;
                    case 'system':
                        $title = $actor .' '
                            . sprintf($activity->verbToText(), _('allen')) .' '
                            . $object_text;
                        break;
                    case 'user':
                        $title = $actor .' '
                            . sprintf($activity->verbToText(), $context_name) .' '
                            . $object_text;
                        break;
                }

                return array(
                    'text' => $title,
                    'verb' => $activity["verb"],
                    'user_id' => $activity["actor_id"],
                    'name' => User::find($activity["actor_id"])->getFullName(),
                    'url' => $url
                );
            }
        }
        return false;
    }
}