<?php

class AddCronjobForAutomaticTurns extends Migration
{
    function up() {
        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `diplomacyfutureturns` (
                `turn_id` varchar(32) NOT NULL,
                `Seminar_id` varchar(32) NOT NULL,
                `name` varchar(64) DEFAULT NULL,
                `description` text DEFAULT NULL,
                `start_time` bigint(20) NOT NULL,
                `whenitsdone` tinyint DEFAULT '0' NOT NULL,
                `chdate` bigint(20) NOT NULL,
                `mkdate` bigint(20) NOT NULL,
                PRIMARY KEY (`turn_id`),
                KEY `Seminar_id` (`Seminar_id`)
            ) ENGINE=MyISAM;
        ");

        DBManager::get()->exec("
            ALTER TABLE `diplomacycommands`
            ADD `iamdone` tinyint DEFAULT '0' NOT NULL AFTER `content`;
        ");

        $new_job = array(
            'filename'    => 'public/plugins_packages/RasmusFuhse/Diplomacy/automatic_turn.cronjob.php',
            'class'       => 'AutomaticTurnJob',
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
        DBManager::get()->exec("DROP TABLE IF EXISTS `diplomacyfutureturns` ");
    }
    
    
}