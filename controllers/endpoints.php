<?php

class EndpointsController extends PluginController {

    public function hook_action($hook_id)
    {
        Navigation::activateItem("/tools/hooks");
        PageLayout::setTitle(_("Log Details"));
        $this->hook = new Hook($hook_id);
        if (!Request::isPost() || ($this->hook['if_type'] !== "IfWebHook")
                || (Request::get("s") !== IfWebHook::getSecurityHash($hook_id)) || !$this->hook['activated']) {
            throw new AccessDeniedException();
        }
        $if_hook = new $this->hook['if_type']();

        $body = file_get_contents('php://input');
        $request = json_decode($body) ?: $_POST;

        $parameters = $if_hook->check($this->hook, "Webhook", "", $request);
        if (is_array($parameters)) {
            $then = new $this->hook['then_type']();
            $output = "Webhook-request-body:\n" . $body . "\n\n" . $then->perform($this->hook, $parameters);
            $this->hook['last_triggered'] = time();
            $this->hook->store();

            $log = new HookLog();
            $log['log_text'] = (string) $output;
            $log['user_id'] = $GLOBALS['user']->id;
            $log['hook_id'] = $this->hook->getId();
            $log->store();
            HookLog::cleanUpLog();
        }

        $this->render_text("ok");
    }

}