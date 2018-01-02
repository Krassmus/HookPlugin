<?php

class ThenWebHook implements ThenHook {

    static public function getName() {
        return _("Webhook");
    }

    public function getEditTemplate(Hook $hook, $attributes) {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("thens/webhook/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function perform(Hook $hook, $parameters) {

    }
}