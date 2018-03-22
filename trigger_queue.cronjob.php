<?php

class TriggerQueueJob extends CronJob
{
    /**
     * Returns the name of the cronjob.
     */
    public static function getName()
    {
        return _('Wenn/Dann-Plugin: Löst die Hook-Queue per Cronjob aus.');
    }

    /**
     * Returns the description of the cronjob.
     */
    public static function getDescription()
    {
        return _('Wenn ein Wenn/Dann eine niedrige Priorität hat, wird der Hook in einer Queue gespeichert. Dieser Cronjob führt die Hooks in der Queue der Reihe nach aus.');
    }

    public function setUp() {
        require_once 'lib/visual.inc.php';
        require_once __DIR__."/HookPlugin.class.php";
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
        $async_curl = curl_multi_init();
        foreach ($queue_entries as $queue_entry) {
            $hook = Hook::find($queue_entry['hook_id']);
            $then = new $hook['then_type']();
            $output = $then->perform($hook, $queue_entry->parameters, $async_curl);

            $log = new HookLog();
            $log['log_text'] = $output;
            $log['user_id'] = $queue_entry['user_id'];
            $log['hook_id'] = $hook->getId();
            $log->store();
            $queue_entry->delete();

            $hook['last_triggered'] = time();
            $hook->store();
        }
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
        //curl_multi_remove_handle($async_curl, $ch2);
        curl_multi_close($async_curl);
        HookLog::cleanUpLog();
    }
}
