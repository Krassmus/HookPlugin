<?php

class ThenTelegramHook implements ThenHook {

    protected $bot_url = "https://api.telegram.org/bot420708582:AAFzX-VAnlWHQgd01P_L8aTxwILzn3i9LcU/";

    static public function getName() {
        return _("Telegram-Bot");
    }

    public function getEditTemplate(Hook $hook, $attributes) {
        if ($hook['then_settings']['telegram_user']
                && ($hook['then_settings']['chat_with_user'] !== $hook['then_settings']['telegram_user'])) {
            //get chat_id
            $hook['then_settings']['chat_id'] = "";
            $hook['then_settings']['chat_with_user'] = "";
            $hook->store();
            $url = $this->bot_url."getUpdates";
            $updates = json_decode(file_get_contents($url), true);
            foreach ($updates['result'] as $message) {
                if (($message['message']['from']['username'] === $hook['then_settings']['telegram_user']) && ($message['message']['chat']['id'])){
                    $hook['then_settings']['chat_id'] = $message['message']['chat']['id'];
                    $hook['then_settings']['chat_with_user'] = $message['message']['from']['username'];
                }
            }
            $hook->store();
        }
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("thens/telegram/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function perform(Hook $hook, $parameters) {
        if ($hook['then_settings']['chat_id']) {
            $header = array();

            $header[] = "Content-Type: application/json";

            $r = curl_init();
            curl_setopt($r, CURLOPT_URL, $this->bot_url . "sendMessage");
            curl_setopt($r, CURLOPT_POST, true);
            curl_setopt($r, CURLOPT_HTTPHEADER, $header);
            curl_setopt($r, CURLOPT_RETURNTRANSFER, true);

            $payload = array(
                'chat_id' => $hook['then_settings']['chat_id'],
                'text' => HookPlugin::formatTextTemplate($hook['then_settings']['template'], $parameters)
            );
            curl_setopt($r, CURLOPT_POSTFIELDS, json_encode($payload));

            $result = curl_exec($r);
            curl_close($r);
            $output = "Payload: " . json_encode($payload) . "\n\nAntwort vom Server: " . $result;
        } else {
            $output = "Konnte nichts nach Telegram schicken, weil noch keine chat_id bekannt ist.";
        }
        return $output;
    }
}