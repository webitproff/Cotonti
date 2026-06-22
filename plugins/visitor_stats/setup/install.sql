-- Visitor Statistics Tables
-- Main visitor tracking table
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

-- Daily statistics cache for faster queries
CREATE TABLE IF NOT EXISTS `cot_visitor_stats_daily` (
  `vsd_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vsd_date` DATE NOT NULL,
  `vsd_total_visits` INT UNSIGNED DEFAULT 0,
  `vsd_human_visits` INT UNSIGNED DEFAULT 0,
  `vsd_bot_visits` INT UNSIGNED DEFAULT 0,
  `vsd_unique_visitors` INT UNSIGNED DEFAULT 0,
  `vsd_updated_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`vsd_id`),
  UNIQUE KEY `vsd_date` (`vsd_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bot/Crawler statistics
CREATE TABLE IF NOT EXISTS `cot_visitor_stats_crawlers` (
  `vsc_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vsc_date` DATE NOT NULL,
  `vsc_crawler_name` VARCHAR(255) NOT NULL,
  `vsc_visits` INT UNSIGNED DEFAULT 0,
  `vsc_updated_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`vsc_id`),
  UNIQUE KEY `vsc_date_crawler` (`vsc_date`, `vsc_crawler_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;