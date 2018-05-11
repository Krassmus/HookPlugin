<?php

class AddCronjob extends Migration
{
    function up() {
        $new_job = array(
            'filename'    => 'public/plugins_packages/RasmusFuhse/HookPlugin/trigger_queue.cronjob.php',
            'class'       => 'TriggerQueueJob',
            'priority'    => 'normal',
            'minute'      => '-1'
        );

        $query = "INSERT IGNORE INTO `cronjobs_tasks`
                    (`task_id`, `filename`, `class`, `active`)
                  VALUES (:task_id, :filename, :class, 1)";
        $task_statement = DBManager::get()->prepare($query);

        $query = "INSERT IGNORE INTO `cronjobs_schedules`
                    (`schedule_id`, `task_id`, `parameters`, `priority`,
                     `type`, `minute`, `mkdate`, `chdate`,
                     `last_result`)
                  VALUES (:schedule_id, :task_id, '[]', :priority, 'periodic',
                          :minute, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
                          NULL)";
        $schedule_statement = DBManager::get()->prepare($query);


        $task_id = md5(uniqid('task', true));

        $task_statement->execute(array(
            ':task_id'  => $task_id,
            ':filename' => $new_job['filename'],
            ':class'    => $new_job['class'],
        ));

        $schedule_id = md5(uniqid('schedule', true));
        $schedule_statement->execute(array(
            ':schedule_id' => $schedule_id,
            ':task_id'     => $task_id,
            ':priority'    => $new_job['priority'],
            ':minute'      => $new_job['minute'],
        ));

        Config::get()->create("HOOKS_FORCE_CRONJOBS", array(
            'value' => 1,
            'type' => "boolean",
            'range' => "global",
            'section' => "HOOKPLUGIN"
        ));

        $new_job = array(
            'filename'    => 'public/plugins_packages/RasmusFuhse/HookPlugin/trigger_nightly.cronjob.php',
            'class'       => 'TriggerNightlyJob',
            'priority'    => 'normal',
            'minute'      => '-1'
        );

        $query = "INSERT IGNORE INTO `cronjobs_tasks`
                    (`task_id`, `filename`, `class`, `active`)
                  VALUES (:task_id, :filename, :class, 1)";
        $task_statement = DBManager::get()->prepare($query);

        $query = "INSERT IGNORE INTO `cronjobs_schedules`
                    (`schedule_id`, `task_id`, `parameters`, `priority`,
                     `type`, `minute`, `mkdate`, `chdate`,
                     `last_result`)
                  VALUES (:schedule_id, :task_id, '[]', :priority, 'periodic',
                          :minute, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
                          NULL)";
        $schedule_statement = DBManager::get()->prepare($query);


        $task_id = md5(uniqid('task', true));

        $task_statement->execute(array(
            ':task_id'  => $task_id,
            ':filename' => $new_job['filename'],
            ':class'    => $new_job['class'],
        ));

        $schedule_id = md5(uniqid('schedule', true));
        $schedule_statement->execute(array(
            ':schedule_id' => $schedule_id,
            ':task_id'     => $task_id,
            ':priority'    => $new_job['priority'],
            ':minute'      => $new_job['minute'],
        ));
    }
    
    function down() {
        DBManager::get()->exec("
            DELETE FROM `config` WHERE `config_id` = MD5('HOOKS_FORCE_CRONJOBS')
        ");
    }
    
    
}