<?php

class TriggerNightlyJob extends CronJob
{
    /**
     * Returns the name of the cronjob.
     */
    public static function getName()
    {
        return _('Wenn/Dann-Plugin: Aufgaben, die einmal in der Nacht ausgeführt werden sollen.');
    }

    /**
     * Returns the description of the cronjob.
     */
    public static function getDescription()
    {
        return _('Wenn ein Wenn/Dann im Wenn-Teil stehen hat, dass er einmal in der Nacht ausgeführt werden soll, so muss dieser Cronjob ihn anstoßen.');
    }

    public function setUp() {
        require_once 'lib/visual.inc.php';
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
        $hooks = Hook::findBySQL("if_type = 'IfNightlyHook' ORDER BY RAND()");
        foreach ($hooks as $hook) {
            $minutes_after_midnight = floor((time() - mktime($hour = 0, $minute = 0, $second = 0)) / 60);
            echo "Plan in Minuten nach Mitternacht: ";
            if ((($hook['if_settings']['minutes_after_midnight'] <= $minutes_after_midnight) || (time() - (int) $hook['if_settings']['last_execution'] > 37 * 60 * 60))
                    && (true || $minutes_after_midnight < 60 * 7)) {
                //only between 0 and 7 AM and only if it's either late enough or cronjob didn't start last night.
                $then = new $hook['then_type']();
                $output = $then->perform($hook, array());

                $hook['if_settings']['last_execution'] = time();
                $hook->store();

                $log = new HookLog();
                $log['log_text'] = $output;
                $log['user_id'] = "IfNightlyHook";
                $log['hook_id'] = $hook->getId();
                $log->store();
            }
        }
        HookLog::cleanUpLog();
    }
}
