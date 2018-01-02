<?php

require_once __DIR__."/lib/Hook.php";
require_once __DIR__."/lib/IfHook.interface.php";
require_once __DIR__."/lib/ThenHook.interface.php";
foreach (scandir(__DIR__."/lib/if_hooks") as $file) {
    if ($file[0] !== ".") {
        include_once __DIR__."/lib/if_hooks/".$file;
    }
}
foreach (scandir(__DIR__."/lib/then_hooks") as $file) {
    if ($file[0] !== ".") {
        include_once __DIR__."/lib/then_hooks/".$file;
    }
}

class HookPlugin extends StudIPPlugin implements SystemPlugin
{
    public function __construct()
    {
        parent::__construct();
        $tooltab = new Navigation(_("Wenn/Dann"), PluginEngine::getURL($this, array(), "hooks/overview"));
        Navigation::addItem("/tools/hooks", $tooltab);
        NotificationCenter::addObserver($this, "checkHooksToTrigger", NULL);
        NotificationCenter::addObserver($this, "checkHooksToTrigger", "MessageDidCreate");
    }

    public function checkHooksToTrigger($event, $object)
    {
        $hooks = array();
        foreach (get_declared_classes() as $class) {
            if (in_array('IfHook', class_implements($class))) {
                $hookobject = new $class();
                if (in_array($event, $hookobject->listenToNotificationEvents())) {
                    if ($hookobject->userIdField()) {
                        $hooks += Hook::findBySQL("if_type = ? AND user_id = ?", array($class, $object[$hookobject->userIdField()]));
                    } else {
                        $hooks += Hook::findBySQL("if_type = ?", array($class));
                    }
                }
            }
        }
        foreach ($hooks as $hook) {
            try {
                $ifhook = new $hook['if_type']();
                $parameters = $ifhook->check($hook, "NotificationCenter", $event, $object);
                if (is_array($parameters)) {
                    $then = new $hook['then_type']();
                    $then->perform($hook, $parameters);
                    $hook['last_triggered'] = time();
                    $hook->store();
                }
            } catch(Exception $e) {
                //logging
                var_dump($e->getMessage());
            }
        }
    }
}