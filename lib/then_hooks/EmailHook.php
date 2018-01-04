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
        $subject = HookPlugin::formatTextTemplate($hook['then_settings']['subject'], $parameters);
        $body = HookPlugin::formatTextTemplate($hook['then_settings']['body'], $parameters);
        StudipMail::sendMessage(
            $hook['then_settings']['to_email'],
            $subject,
            $body
        );
        return sprintf("Mail wurde versendet an %s\nBetreff: %s\n\nNachricht:\n%s", $hook['then_settings']['to_email'], $subject, $body);
    }
}