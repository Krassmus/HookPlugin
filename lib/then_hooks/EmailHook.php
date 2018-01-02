<?php

class EmailHook implements ThenHook {

    static public function getName() {
        return _("Email senden");
    }

    public function getEditTemplate(Hook $hook, $attributes) {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("thens/emailhook/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function perform(Hook $hook, $parameters) {
        $subject = $hook['then_settings']['subject'];
        $body = $hook['then_settings']['body'];
        foreach ($parameters as $parameter => $value) {
            $subject = str_replace("{{".$parameter."}}", $value, $subject);
            $body = str_replace("{{".$parameter."}}", $value, $body);
        }
        StudipMail::sendMessage(
            $hook['then_settings']['to_email'],
            $subject,
            $body
        );
    }
}