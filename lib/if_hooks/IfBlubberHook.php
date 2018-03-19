<?php

class IfBlubberHook implements IfHook
{

    static public function getName()
    {
        return _("Blubber");
    }

    public function getParameters(Hook $hook)
    {
        return array("von_id", "von_name", "nachricht", "url", "blubber_id", "thread_id", "context_type", "context_id");
    }

    public function listenToNotificationEvents()
    {
        return array("BlubberPostingDidCreate", "BlubberPostingDidStore", "BlubberPostingDidDelete");
    }

    public function findHooksByObject($object)
    {
        switch ($object['context_type']) {
            case "public":
                return Hook::findBySQL("if_type = ? AND activated = '1' ", array(get_class($this)));
            case "private":
                return Hook::findBySQL("if_type = ? AND activated = '1' ", array(get_class($this)));
                //It came too early, huh huh!
                $statement = DBManager::get()->prepare("
                    SELECT user_id
                    FROM blubber_mentions
                    WHERE topic_id = ?
                        AND external_contact = '0'
                ");
                $statement->execute(array($object->getId()));
                $user_ids = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
                return Hook::findBySQL("if_type = ? AND activated = '1' AND user_id IN (?) ", array(
                    get_class($this),
                    $user_ids
                ));
            case "course":
                $statement = DBManager::get()->prepare("
                    SELECT hooks.*
                    FROM hooks 
                        INNER JOIN seminar_user ON (seminar_user.user_id = hooks.user_id)
                    WHERE seminar_user.Seminar_id = ?
                ");
                $statement->execute(array($object['seminar_id']));
                $hooks = array();
                foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
                    $hooks[] = Hook::buildExisting($data);
                }
                return $hooks;
        }
    }

    public function getEditTemplate(Hook $hook)
    {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("ifs/blubber/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function check(Hook $hook, $type, $event, $blubber)
    {
        $oldbase = URLHelper::setBaseURL($GLOBALS['ABSOLUTE_URI_STUDIP']);
        $url = URLHelper::getURL("plugins.php/blubber/streams/thread/".$blubber['root_id']);
        URLHelper::setBaseURL($oldbase);
        return array(
            'von_id' => $blubber['user_id'],
            'von_name' => User::find($blubber['user_id'])->getFullName(),
            'blubber_id' => $blubber->getId(),
            'thread_id' => $blubber['root_id'],
            'nachricht' => $blubber['description'],
            'context_type' => $blubber['context_type'],
            'context_id' => $blubber['Seminar_id'],
            'url' => $url
        );
    }
}