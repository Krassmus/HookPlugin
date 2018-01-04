CREATE TABLE `hooks` (
  `hook_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `activated` tinyint(4) DEFAULT '1',
  `cronjob` tinyint(4) DEFAULT '1',
  `user_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `if_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `if_settings` text COLLATE utf8mb4_unicode_ci,
  `then_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `then_settings` text COLLATE utf8mb4_unicode_ci,
  `general_settings` text COLLATE utf8mb4_unicode_ci,
  `last_triggered` int(11) DEFAULT NULL,
  `chdate` int(11) NOT NULL,
  `mkdate` int(11) NOT NULL,
  PRIMARY KEY (`hook_id`)
) ENGINE=InnoDB;

CREATE TABLE `hooks_log` (
  `log_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `hook_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `log_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` tinyint(4) NOT NULL DEFAULT '0',
  `mkdate` int(11) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB