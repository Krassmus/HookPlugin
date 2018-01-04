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
        $header = array();

        $header[] = "Content-Type: application/json";

        $r = curl_init();
        curl_setopt($r, CURLOPT_URL, $hook['then_settings']['webhook_url']);
        curl_setopt($r, CURLOPT_POST, true);
        curl_setopt($r, CURLOPT_HTTPHEADER, $header);
        curl_setopt($r, CURLOPT_RETURNTRANSFER, true);

        $payload = array();
        foreach ($hook['then_settings']['json']['keys'] as $i => $key) {
            if (trim($key)) {
                $value = HookPlugin::formatTextTemplate($hook['then_settings']['json']['values'][$i], $parameters);
                $payload[$key] = $value;
            }
        }

        curl_setopt($r, CURLOPT_POSTFIELDS, json_encode($payload));

        $result = curl_exec($r);
        curl_close($r);
        $output = "Payload: ".json_encode($payload)."\n\nAntwort vom Server: ".$result;
        return $output;
    }
}