<?php

require_once __DIR__."/lib/Hook.php";
require_once __DIR__."/lib/HookLog.php";
require_once __DIR__."/lib/HookQueue.php";
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
require_once __DIR__."/lib/HooksRouteMap.php";

class HookPlugin extends StudIPPlugin implements SystemPlugin, RESTAPIPlugin
{

    public function getRouteMaps()
    {
        return [
            new HooksRouteMap(),
        ];
    }


    static public function formatTextTemplate($text, $parameters) {
        foreach ($parameters as $parameter => $value) {
            $text = str_replace("{{".$parameter."}}", $value, $text);
        }
        $functions = array("md5", "rawurlencode", "urlencode", "htmlReady", "formatReady");
        foreach ($functions as $function) {
            $text = preg_replace_callback(
                "/".strtoupper($function)."\((.*)\)/",
                function ($match) use ($function) {
                    return $function($match[1]);
                },
                $text
            );
        }
        return $text;
    }

    public function __construct()
    {
        parent::__construct();
        if (Navigation::hasItem("/tools") && Config::get()->HOOKS_ALLOW_GUI) {
            $tooltab = new Navigation(_("Wenn/Dann"), PluginEngine::getURL($this, array(), "hooks/overview"));
            Navigation::addItem("/tools/hooks", $tooltab);
        }
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
                    $hooks += $hookobject->findHooksByObject($object);
                }
            }
        }
        $async_curl = curl_multi_init();
        $curl_handles = array();
        $added = false;
        foreach ($hooks as $hook) {
            if ($hook['activated']) {
                try {
                    $ifhook = new $hook['if_type']();
                    $parameters = $ifhook->check($hook, "NotificationCenter", $event, $object);
                    if (is_array($parameters)) {
                        if (($hook['cronjob'] || Config::get()->HOOKS_FORCE_CRONJOBS) && Config::get()->CRONJOBS_ENABLE) {
                            $queue_entry = new HookQueue();
                            $queue_entry['hook_id'] = $hook->getId();
                            $queue_entry['parameters'] = $parameters;
                            $queue_entry['user_id'] = $GLOBALS['user']->id;
                            $queue_entry->store();
                        } else {
                            $then = new $hook['then_type']();
                            $output = $then->perform($hook, $parameters, $async_curl);
                            if (is_resource($output)) {
                                $added = true;
                                $curl_handles[] = $output;
                                $output = "curl_multi_init";
                            }
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
                } catch (Exception $e) {
                    //logging
                    $log = new HookLog();
                    $log['exception'] = 1;
                    $log['log_text'] = $e->getMessage();
                    $log['user_id'] = $GLOBALS['user']->id;
                    $log['hook_id'] = $hook->getId();
                    $log->store();
                    HookLog::cleanUpLog();
                }
            }
        }

        if ($added) {
            $active = null;
            do {
                $mrc = curl_multi_exec($async_curl, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            while ($active && $mrc == CURLM_OK) {
                if (curl_multi_select($async_curl) != -1) {
                    do {
                        $mrc = curl_multi_exec($async_curl, $active);
                    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                }
            }
            foreach ($curl_handles as $handle) {
                curl_multi_remove_handle($async_curl, $handle);
            }
            curl_multi_close($async_curl);
        }
    }
}