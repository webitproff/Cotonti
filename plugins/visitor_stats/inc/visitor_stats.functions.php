<?php
/**
 * Visitor Statistics Functions
 *
 * @package VisitorStats
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('visitor_stats', 'plug', 'inc/CrawlerDetectService');
require_once cot_incfile('visitor_stats', 'plug', 'inc/VisitorStatsRepository');
require_once cot_incfile('visitor_stats', 'plug', 'inc/VisitorStatsService');

Cot::$db->registerTable('visitor_stats');
Cot::$db->registerTable('visitor_stats_daily');
Cot::$db->registerTable('visitor_stats_crawlers');
?>