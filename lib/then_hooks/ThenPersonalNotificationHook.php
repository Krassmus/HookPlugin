<?php

class ThenPersonalNotificationHook implements ThenHook {

    static public function getName() {
        return _("Stud.IP-Benachrichtigung");
    }

    public function getEditTemplate(Hook $hook, $attributes) {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("thens/personalnotification/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function perform(Hook $hook, $parameters) {
        $success = PersonalNotifications::add(
            $hook['user_id'],
            HookPlugin::formatTextTemplate($hook['then_settings']['url'], $parameters),
            HookPlugin::formatTextTemplate($hook['then_settings']['text'], $parameters),
            null,
            HookPlugin::formatTextTemplate($hook['then_settings']['avatar'], $parameters) ?: $GLOBALS['ABSOLUTE_URI_STUDIP']."plugins_packages/RasmusFuhse/HookPlugin/assets/webhook_blue.svg",
            $hook['then_settings']['dialog']
        );
        if (!$success) {
            throw new Exception("Konnte Benachrichtigung nicht abschicken.");
        }
        return "Benachrichtigung wurde versendet.";
    }
}