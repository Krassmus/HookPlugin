<?php

class IfMessageHook implements IfHook {

    static public function getName()
    {
        return _("Stud.IP-Nachricht");
    }

    public function getParameters(Hook $hook)
    {
        return array("von_id", "von_name", "von_mail", "betreff", "nachricht");
    }

    public function listenToNotificationEvents()
    {
        return array("MessageDidCreate", "MessageDidSend");
    }

    public function findHooksByObject($object)
    {
        return Hook::findBySQL("INNER JOIN message_user USING (user_id) WHERE message_user.message_id = ? AND if_type = ? AND activated = '1' AND message_user.user_id = ? ", array(is_string($object) ? $object : $object->getId(), get_class($this)));
    }

    public function getEditTemplate(Hook $hook)
    {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("ifs/ifmessage/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function check(Hook $hook, $type, $event, $message)
    {
        if (is_string($message)) {
            $message = Message::find($message);
        }
        $adressees = $message->receivers->pluck("user_id");
        if (in_array($hook['user_id'], $adressees)) {
            if ($hook['if_settings']['tag_filter']) {
                $statement = DBManager::get()->prepare("
                    SELECT tag 
                    FROM message_tags 
                    WHERE message_id = :message_id 
                        AND user_id = :user_id
                ");
                $statement->execute(array(
                    'message_id' => $message->getId(),
                    'user_id' => $hook['user_id']
                ));
                $tags = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
            }
            if (!$hook['if_settings']['tag_filter'] || in_array($hook['if_settings']['tag_filter'], $tags)) {
                return array(
                    'von_id' => $message['autor_id'],
                    'von_name' => $message['autor_id'] === "___system___" ? "System" : User::find($message['autor_id'])->getFullName(),
                    'von_mail' => $message['autor_id'] === "___system___" ? "System" : User::find($message['autor_id'])->email,
                    'betreff' => $message['subject'],
                    'nachricht' => $message['message']
                );
            }
        }
        return false;
    }
}