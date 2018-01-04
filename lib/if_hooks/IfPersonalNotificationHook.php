<?php

class IfPersonalNotificationHook implements IfHook {

    static public function getName() {
        return _("Stud.IP-Benachrichtigung");
    }

    public function getParameters(Hook $hook)
    {
        return array("avatar", "url", "nachricht");
    }

    public function getEditTemplate(Hook $hook) {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("ifs/personalnotification/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function listenToNotificationEvents()
    {
        return array("PersonalNotificationsDidStore", "PersonalNotificationsDidAssign");
    }

    public function findHooksByObject($object)
    {
        return Hook::findBySQL("INNER JOIN personal_notifications_user USING (user_id) WHERE personal_notifications_user.personal_notification_id = ? AND if_type = ? AND activated = '1'", array($object->getId(), get_class($this)));
    }

    public function check(Hook $hook, $type, $event, $notification) {
        $statement = DBManager::get()->prepare("
            SELECT 1 
            FROM personal_notifications_user 
            WHERE user_id = ?
                AND personal_notification_id = ?
        ");
        $statement->execute(array($hook['user_id'], $notification->getId()));
        if ($statement->fetch()) {
            return array(
                'avatar' => $notification['avatar'],
                'nachricht' => $notification['text'],
                'url' => $notification['url']
            );
        } else {
            return false;
        }
    }
}