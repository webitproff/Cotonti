<?php
/**
 * Crawler Detection Service
 * Wrapper around Crawler-Detect library
 *
 * @package VisitorStats
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('visitor_stats', 'plug', 'lib/CrawlerDetect');

class CrawlerDetectService
{
    private static $instance = null;
    private $crawlerDetect;

    private function __construct()
    {
        $this->crawlerDetect = new CrawlerDetect();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check if current visitor is a crawler/bot
     *
     * @return bool
     */
    public function isCrawler()
    {
        return $this->crawlerDetect->isCrawler();
    }

    /**
     * Get crawler name if detected
     *
     * @return string|null
     */
    public function getCrawlerName()
    {
        if ($this->crawlerDetect->isCrawler()) {
            $match = $this->crawlerDetect->getMatches();
            return !empty($match) ? trim($match) : null;
        }
        return null;
    }

    /**
     * Get current user agent
     *
     * @return string|null
     */
    public function getUserAgent()
    {
        return $this->crawlerDetect->getUserAgent();
    }
}
?>