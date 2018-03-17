<?php

class LogsController extends PluginController {

    public function overview_action($hook_id = null)
    {
        Navigation::activateItem("/tools/hooks");
        if ($hook_id) {
            $this->logs = HookLog::findBySQL("hook_id = ? ORDER BY hooks_log.mkdate DESC", array($hook_id));
        } else {
            $this->logs = HookLog::findBySQL("INNER JOIN hooks USING (hook_id) WHERE hooks.user_id = ? ORDER BY hooks_log.mkdate DESC", array($GLOBALS['user']->id));
        }
        PageLayout::setTitle(_("Logs"));
    }

    public function details_action($log_id)
    {
        Navigation::activateItem("/tools/hooks");
        PageLayout::setTitle(_("Log Details"));
        $this->log = new HookLog($log_id);
        if (!$this->log->hook["editable"]) {
            throw new AccessDeniedException();
        }
    }

}