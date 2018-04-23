<?php

class ThenSocketHook implements ThenHook {

    static public function getName() {
        return _("Websocket");
    }

    public function getEditTemplate(Hook $hook, $attributes) {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("thens/socket/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function perform(Hook $hook, $parameters, $socket_stream = null) {
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
            $payload = json_decode($hook['then_settings']['json'], true);
            $payload = $this->recursiveTemplatize($payload, $parameters);
            $payload = json_encode($payload);
        }
        if (trim($hook['then_settings']['cert'])) {
            $file = $GLOBALS['TMP_PATH']."/".$hook->getId().".pem";
            file_put_contents($file, $hook['then_settings']['cert']);
            //curl_setopt($r, CURLOPT_CAINFO, $file);
            curl_setopt($r, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($r, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($r, CURLOPT_SSLCERT, $file);
        }


        curl_setopt($r, CURLOPT_POSTFIELDS, $payload);

        if ($socket_stream) {
            curl_multi_add_handle($socket_stream, $r);
            return $r;
        } else {
            $result = curl_exec($r);
            curl_close($r);
            $output = "Payload: " . json_encode($payload) . "\n\nAntwort vom Server: " . $result;
            return $output;
        }
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