<?php
/**
 * Visitor Statistics Service
 * Main business logic
 *
 * @package VisitorStats
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

class VisitorStatsService
{
    private static $instance = null;
    private $crawlerDetect;
    private $repository;

    private function __construct()
    {
        $this->crawlerDetect = CrawlerDetectService::getInstance();
        $this->repository = VisitorStatsRepository::getInstance();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Record a visit
     */
    public function recordVisit()
    {
        // Check if crawler
        $is_crawler = $this->crawlerDetect->isCrawler();
        $crawler_name = $is_crawler ? $this->crawlerDetect->getCrawlerName() : null;

        $visit_data = [
            'vs_date' => Cot::$sys['now'],
            'vs_ip' => $this->getClientIp(),
            'vs_user_id' => Cot::$usr['id'] > 0 ? (int)Cot::$usr['id'] : 0,
            'vs_referer' => isset($_SERVER['HTTP_REFERER']) ? substr($_SERVER['HTTP_REFERER'], 0, 500) : '',
            'vs_user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 500) : '',
            'vs_page' => isset($_SERVER['REQUEST_URI']) ? substr($_SERVER['REQUEST_URI'], 0, 500) : '',
            'vs_crawler_name' => $crawler_name,
        ];

        $this->repository->insert($visit_data);
    }

    /**
     * Get statistics for period
     *
     * @param int $days
     * @param bool $exclude_bots
     * @return array
     */
    public function getStatsForPeriod($days = 30, $exclude_bots = true)
    {
        return [
            'total_visits' => $this->repository->countVisits($days, false),
            'bot_visits' => $this->repository->countBotVisits($days),
            'human_visits' => $this->repository->countVisits($days, true),
            'unique_visitors' => $this->repository->countUniqueVisitors($days),
            'top_pages' => $this->repository->getTopPages($days, 10),
            'top_referers' => $this->repository->getTopReferers($days, 10),
            'top_crawlers' => $this->repository->getTopCrawlers($days, 10),
            'daily' => $this->repository->getDailyBreakdown($days),
        ];
    }

    /**
     * Get client IP address
     *
     * @return string
     */
    private function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return trim(explode(',', $_SERVER['HTTP_CLIENT_IP'])[0]);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
?>