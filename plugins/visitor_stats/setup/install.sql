CREATE TABLE IF NOT EXISTS `cot_visitor_stats` (
  `vs_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vs_date` INT UNSIGNED NOT NULL,
  `vs_ip` VARCHAR(45) NOT NULL,
  `vs_user_id` INT UNSIGNED DEFAULT 0,
  `vs_referer` VARCHAR(500),
  `vs_user_agent` VARCHAR(500),
  `vs_page` VARCHAR(500),
  `vs_crawler_name` VARCHAR(255),
  PRIMARY KEY (`vs_id`),
  KEY `vs_date` (`vs_date`),
  KEY `vs_ip` (`vs_ip`),
  KEY `vs_user_id` (`vs_user_id`),
  KEY `vs_crawler` (`vs_crawler_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;