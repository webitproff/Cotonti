<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Administration panel - Visitor Statistics
 *
 * @package VisitorStats
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('plug', 'visitor_stats');
cot_block(Cot::$usr['auth_read']);

require_once cot_langfile('visitor_stats', 'plug');
require_once cot_incfile('visitor_stats', 'plug');

$tt = new XTemplate(cot_tplfile('visitor_stats.admin', 'plug', true));

$adminTitle = Cot::$L['visitor_stats'];

// Get filter parameters
$period = cot_import('period', 'G', 'TXT', 'day');
$days = cot_import('days', 'G', 'INT', 30);
$exclude_bots = cot_import('exclude_bots', 'G', 'INT', 1);

/* === Hook === */
foreach (cot_getextplugins('visitor_stats.admin.first') as $pl) {
    include $pl;
}
/* ===== */

$visitorService = VisitorStatsService::getInstance();

// Get statistics
$stats = $visitorService->getStatsForPeriod($days, (bool)$exclude_bots);

// Assign to template
$tt->assign([
    'VISITOR_STATS_TOTAL_VISITS' => $stats['total_visits'],
    'VISITOR_STATS_BOT_VISITS' => $stats['bot_visits'],
    'VISITOR_STATS_HUMAN_VISITS' => $stats['human_visits'],
    'VISITOR_STATS_UNIQUE_VISITORS' => $stats['unique_visitors'],
    'VISITOR_STATS_PERIOD' => $period,
    'VISITOR_STATS_DAYS' => $days,
    'VISITOR_STATS_EXCLUDE_BOTS' => $exclude_bots,
]);

// Daily breakdown
if (!empty($stats['daily'])) {
    $max_hits = max(array_column($stats['daily'], 'count'));
    $ii = 0;
    foreach ($stats['daily'] as $day_data) {
        $percentbar = $max_hits > 0 ? floor(($day_data['count'] / $max_hits) * 100) : 0;
        $tt->assign([
            'VISITOR_STATS_DAY_DATE' => $day_data['date'],
            'VISITOR_STATS_DAY_COUNT' => $day_data['count'],
            'VISITOR_STATS_DAY_BOTS' => $day_data['bots'],
            'VISITOR_STATS_DAY_HUMANS' => $day_data['humans'],
            'VISITOR_STATS_DAY_PERCENTBAR' => $percentbar,
            'VISITOR_STATS_DAY_ODDEVEN' => cot_build_oddeven($ii),
        ]);
        $tt->parse('MAIN.DAILY_BREAKDOWN.ROW');
        $ii++;
    }
    $tt->parse('MAIN.DAILY_BREAKDOWN');
}

// Top pages
if (!empty($stats['top_pages'])) {
    $ii = 0;
    foreach ($stats['top_pages'] as $page) {
        $tt->assign([
            'VISITOR_STATS_PAGE_URL' => htmlspecialchars($page['page']),
            'VISITOR_STATS_PAGE_COUNT' => $page['count'],
            'VISITOR_STATS_PAGE_ODDEVEN' => cot_build_oddeven($ii),
        ]);
        $tt->parse('MAIN.TOP_PAGES.ROW');
        $ii++;
    }
    $tt->parse('MAIN.TOP_PAGES');
}

// Top referrers
if (!empty($stats['top_referers'])) {
    $ii = 0;
    foreach ($stats['top_referers'] as $ref) {
        $referer_display = !empty($ref['referer']) ? htmlspecialchars($ref['referer']) : Cot::$L['visitor_stats_direct'];
        $tt->assign([
            'VISITOR_STATS_REFERER' => $referer_display,
            'VISITOR_STATS_REFERER_COUNT' => $ref['count'],
            'VISITOR_STATS_REFERER_ODDEVEN' => cot_build_oddeven($ii),
        ]);
        $tt->parse('MAIN.TOP_REFERERS.ROW');
        $ii++;
    }
    $tt->parse('MAIN.TOP_REFERERS');
}

// Top crawlers
if (!empty($stats['top_crawlers'])) {
    $ii = 0;
    foreach ($stats['top_crawlers'] as $crawler) {
        $tt->assign([
            'VISITOR_STATS_CRAWLER_NAME' => $crawler['crawler_name'] ?? Cot::$L['visitor_stats_unknown'],
            'VISITOR_STATS_CRAWLER_COUNT' => $crawler['count'],
            'VISITOR_STATS_CRAWLER_ODDEVEN' => cot_build_oddeven($ii),
        ]);
        $tt->parse('MAIN.TOP_CRAWLERS.ROW');
        $ii++;
    }
    $tt->parse('MAIN.TOP_CRAWLERS');
}

/* === Hook === */
foreach (cot_getextplugins('visitor_stats.admin.tags') as $pl) {
    include $pl;
}
/* ===== */

$tt->parse('MAIN');
$pluginBody = $tt->text('MAIN');
?>