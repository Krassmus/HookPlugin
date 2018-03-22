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

    public function perform(Hook $hook, $parameters, $multicurl = null) {
        $header = array();

        $header[] = "Content-Type: application/json";

        foreach ($hook['then_settings']['header']['keys'] as $i => $key) {
            if (strpos($key, "\n") !== false) {
                $key = preg_split("/\r?\n/", $key);
                $key = implode("\n\t". $key);
            }
            $header[] = $i.": " . $key;
        }

        $r = curl_init();
        curl_setopt($r, CURLOPT_URL, $hook['then_settings']['webhook_url']);
        curl_setopt($r, CURLOPT_POST, true);
        curl_setopt($r, CURLOPT_HTTPHEADER, $header);
        curl_setopt($r, CURLOPT_RETURNTRANSFER, true);

        if ($hook['then_settings']['json']) {
            $payload = json_decode($hook['then_settings']['json']);
            $payload = $this->recursiveTemplatize($payload, $parameters);
            $payload = json_encode($payload);
        }

        curl_setopt($r, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($r);
        curl_close($r);
        $output = "Payload: ".json_encode($payload)."\n\nAntwort vom Server: ".$result;
        return $output;
    }

    protected function recursiveTemplatize($value, $parameters) {
        if (is_array($value)) {
            foreach ($value as $key => $v) {
                $key = HookPlugin::formatTextTemplate($key, $parameters);
                $v = $this->recursiveTemplatize($v, $parameters);
                $value[$key] = $v;
            }
            return $value;
        } else {
            return HookPlugin::formatTextTemplate($value, $parameters);
        }
    }
}