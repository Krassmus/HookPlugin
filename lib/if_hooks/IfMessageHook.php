<?php

class IfMessageHook implements IfHook {

    static public function getName() {
        return _("Stud.IP-Nachricht");
    }

    public function getParameters()
    {
        return array("von_id", "von_name", "von_mail", "betreff", "nachricht");
    }

    public function listenToNotificationEvents()
    {
        return array("MessageDidCreate", "MessageDidSend");
    }

    public function userIdField() {
        return null;
    }

    public function getEditTemplate(Hook $hook) {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("ifs/ifmessage/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function check(Hook $hook, $type, $event, $message) {
        if (is_string($message)) {
            $message = Message::find($message);
        }
        $adressees = $message->receivers->pluck("user_id");
        if (in_array($hook['user_id'], $adressees)) {
            return array(
                'von_id' => $message['autor_id'],
                'von_name' => $message['autor_id'] === "___system___" ? "System" : User::find($message['autor_id'])->getFullName(),
                'von_mail' => $message['autor_id'] === "___system___" ? "System" : User::find($message['autor_id'])->email,
                'betreff' => $message['subject'],
                'nachricht' => $message['message']
            );
        } else {
            return false;
        }
    }
}