<?php

class TriggerQueueJob extends CronJob
{
    /**
     * Returns the name of the cronjob.
     */
    public static function getName()
    {
        return _('Wenn/Dann-Plugin: Löst die Hooks per Cronjob aus.');
    }

    /**
     * Returns the description of the cronjob.
     */
    public static function getDescription()
    {
        return _('Wenn ein Wenn/Dann eine niedrige Priorität hat, wird der Hook in einer Queue gespeichert. Dieser Cronjob führt die Hooks in der Queue der Reihe nach aus.');
    }

    public function setUp() {
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
    }

    /**
     * Executes the cronjob.
     *
     * @param mixed $last_result What the last execution of this cronjob
     *                           returned.
     * @param Array $parameters Parameters for this cronjob instance which
     *                          were defined during scheduling.
     *                          Only valid parameter at the moment is
     *                          "verbose" which toggles verbose output while
     *                          purging the cache.
     */
    public function execute($last_result, $parameters = array())
    {
        $queue_entries = HookQueue::findBySQL("1=1 ORDER BY mkdate ASC LIMIT 100");
        foreach ($queue_entries as $queue_entry) {
            $hook = Hook::find($queue_entry['hook_id']);
            $then = new $hook['then_type']();
            $output = $then->perform($hook, $queue_entry->parameters);

            $log = new HookLog();
            $log['log_text'] = $output;
            $log['user_id'] = $queue_entry['user_id'];
            $log['hook_id'] = $hook->getId();
            $log->store();
            HookLog::cleanUpLog();
            $queue_entry->delete();
        }
    }
}
