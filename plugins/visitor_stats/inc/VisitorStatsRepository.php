<?php
/**
 * Visitor Statistics Repository
 * Database operations
 *
 * @package VisitorStats
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

class VisitorStatsRepository
{
    private static $instance = null;

    private function __construct() {}

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Insert visitor record
     *
     * @param array $data
     * @return bool
     */
    public function insert($data)
    {
        try {
            Cot::$db->insert(Cot::$db->visitor_stats, $data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Count total visits in period
     *
     * @param int $days
     * @param bool $exclude_bots
     * @return int
     */
    public function countVisits($days = 30, $exclude_bots = true)
    {
        $start_time = Cot::$sys['now'] - ($days * 86400);
        $query = 'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(Cot::$db->visitor_stats) . ' WHERE vs_date > ?';
        $params = [$start_time];

        if ($exclude_bots) {
            $query .= ' AND vs_crawler_name IS NULL';
        }

        return (int)Cot::$db->query($query, $params)->fetchColumn();
    }

    /**
     * Count bot visits in period
     *
     * @param int $days
     * @return int
     */
    public function countBotVisits($days = 30)
    {
        $start_time = Cot::$sys['now'] - ($days * 86400);
        $query = 'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(Cot::$db->visitor_stats) . ' WHERE vs_date > ? AND vs_crawler_name IS NOT NULL';
        return (int)Cot::$db->query($query, [$start_time])->fetchColumn();
    }

    /**
     * Count unique visitors in period
     *
     * @param int $days
     * @return int
     */
    public function countUniqueVisitors($days = 30)
    {
        $start_time = Cot::$sys['now'] - ($days * 86400);
        $query = 'SELECT COUNT(DISTINCT vs_ip) FROM ' . Cot::$db->quoteTableName(Cot::$db->visitor_stats) . ' WHERE vs_date > ? AND vs_crawler_name IS NULL';
        return (int)Cot::$db->query($query, [$start_time])->fetchColumn();
    }

    /**
     * Get top pages in period
     *
     * @param int $days
     * @param int $limit
     * @return array
     */
    public function getTopPages($days = 30, $limit = 10)
    {
        $start_time = Cot::$sys['now'] - ($days * 86400);
        $query = 'SELECT vs_page as page, COUNT(*) as count FROM ' . Cot::$db->quoteTableName(Cot::$db->visitor_stats)
                . ' WHERE vs_date > ? AND vs_crawler_name IS NULL GROUP BY vs_page ORDER BY count DESC LIMIT ?';
        $result = Cot::$db->query($query, [$start_time, $limit]);
        return $result->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get top referers in period
     *
     * @param int $days
     * @param int $limit
     * @return array
     */
    public function getTopReferers($days = 30, $limit = 10)
    {
        $start_time = Cot::$sys['now'] - ($days * 86400);
        $query = 'SELECT vs_referer as referer, COUNT(*) as count FROM ' . Cot::$db->quoteTableName(Cot::$db->visitor_stats)
                . ' WHERE vs_date > ? AND vs_crawler_name IS NULL GROUP BY vs_referer ORDER BY count DESC LIMIT ?';
        $result = Cot::$db->query($query, [$start_time, $limit]);
        return $result->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get top crawlers in period
     *
     * @param int $days
     * @param int $limit
     * @return array
     */
    public function getTopCrawlers($days = 30, $limit = 10)
    {
        $start_time = Cot::$sys['now'] - ($days * 86400);
        $query = 'SELECT vs_crawler_name as crawler_name, COUNT(*) as count FROM ' . Cot::$db->quoteTableName(Cot::$db->visitor_stats)
                . ' WHERE vs_date > ? AND vs_crawler_name IS NOT NULL GROUP BY vs_crawler_name ORDER BY count DESC LIMIT ?';
        $result = Cot::$db->query($query, [$start_time, $limit]);
        return $result->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get daily breakdown for period
     *
     * @param int $days
     * @return array
     */
    public function getDailyBreakdown($days = 30)
    {
        $start_time = Cot::$sys['now'] - ($days * 86400);
        $query = 'SELECT FROM_UNIXTIME(vs_date, "%Y-%m-%d") as date, COUNT(*) as count,'
                . ' SUM(CASE WHEN vs_crawler_name IS NOT NULL THEN 1 ELSE 0 END) as bots,'
                . ' SUM(CASE WHEN vs_crawler_name IS NULL THEN 1 ELSE 0 END) as humans'
                . ' FROM ' . Cot::$db->quoteTableName(Cot::$db->visitor_stats)
                . ' WHERE vs_date > ? GROUP BY DATE(FROM_UNIXTIME(vs_date)) ORDER BY vs_date DESC';
        $result = Cot::$db->query($query, [$start_time]);
        return $result->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
?>