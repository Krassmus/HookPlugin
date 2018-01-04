<?php

class HooksController extends PluginController {

    public function overview_action()
    {
        Navigation::activateItem("/tools/hooks");
        $this->hooks = Hook::findBySQL("user_id = ? ORDER BY name ASC", array($GLOBALS['user']->id));
        PageLayout::setTitle(_("Wenn/Dann"));
    }

    public function edit_action($hook_id = null)
    {
        Navigation::activateItem("/tools/hooks");
        PageLayout::setTitle(_("Hook bearbeiten"));
        $this->hook = new Hook($hook_id);
        if (!$this->hook->isNew() && $this->hook['user_id'] !== $GLOBALS['user']->id) {
            throw new AccessDeniedException();
        }
        if (Request::isPost()) {
            $data = Request::getArray("data");
            $data['activated'] = (int) $data['activated'];
            $this->hook->setData($data);
            $this->hook['user_id'] = $GLOBALS['user']->id;
            $this->hook->store();
        }
    }

    public function delete_action($hook_id)
    {
        $this->hook = new Hook($hook_id);
        if (!$this->hook->isNew() && $this->hook['user_id'] !== $GLOBALS['user']->id) {
            throw new AccessDeniedException();
        }
        if (Request::isPost()) {
            $this->hook->delete();
            PageLayout::postSuccess(_("Hook wurde erfolgreich gelöscht."));
        }
        $this->redirect("hooks/overview");
    }


    public function edit_if_hook_action($ifhook, $hook_id = null)
    {
        $this->hook = new Hook($hook_id);
        if (!$this->hook->isNew() && $this->hook['user_id'] !== $GLOBALS['user']->id) {
            throw new AccessDeniedException();
        }
        $ifhook = new $ifhook();
        if (!is_a($ifhook, "IfHook")) {
            throw new Exception("Falscher Hook-Name");
        }
        $template = $ifhook->getEditTemplate($this->hook);
        $this->render_text($template->render());
    }

    public function edit_then_hook_action($thenhook, $hook_id = null)
    {
        $this->hook = new Hook($hook_id);
        if (!$this->hook->isNew() && $this->hook['user_id'] !== $GLOBALS['user']->id) {
            throw new AccessDeniedException();
        }
        $thenhook = new $thenhook();
        if (!is_a($thenhook, "ThenHook")) {
            throw new Exception("Falscher Hook-Name");
        }
        $template = $thenhook->getEditTemplate($this->hook, array());
        $this->render_text($template->render());
    }

}