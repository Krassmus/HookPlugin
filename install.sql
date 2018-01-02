CREATE TABLE `hooks` (
  `hook_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `cronjob` tinyint(4) DEFAULT '1',
  `user_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `if_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `if_settings` text COLLATE utf8mb4_unicode_ci,
  `then_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `then_settings` text COLLATE utf8mb4_unicode_ci,
  `last_triggered` int(11) DEFAULT NULL,
  `chdate` int(11) NOT NULL,
  `mkdate` int(11) NOT NULL,
  PRIMARY KEY (`hook_id`)
) ENGINE=InnoDB