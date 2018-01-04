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

        $parameters = $if_hook->check($this->hook, "Webhook", "", $_POST);
        if (is_array($parameters)) {
            $then = new $this->hook['then_type']();
            $output = $then->perform($this->hook, $parameters);
            $hook['last_triggered'] = time();
            $hook->store();

            $log = new HookLog();
            $log['log_text'] = $output;
            $log['user_id'] = $GLOBALS['user']->id;
            $log['hook_id'] = $hook->getId();
            $log->store();
            HookLog::cleanUpLog();
        }

    }

}