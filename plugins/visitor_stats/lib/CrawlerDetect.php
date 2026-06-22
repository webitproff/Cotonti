<?php
/**
 * Crawler-Detect library
 * Embedded version without Composer dependency
 * 
 * @package VisitorStats
 * @copyright (c) Mark Beech
 * @license MIT
 */

namespace CrawlerDetect;

class CrawlerDetect
{
    protected $userAgent;
    protected $httpHeaders = [];
    protected $matches = [];
    protected static $crawlers = [
        'Googlebot', 'Bingbot', 'Slurp', 'DuckDuckGo', 'Baiduspider',
        'YandexBot', 'YandexMobileBot', 'FacebookExternalHit', 'Twitterbot',
        'LinkedInBot', 'WhatsApp', 'Telegram', 'AppleBot', 'Qwantify',
        'Crawl', 'Spider', 'Bot', 'Crawler', 'Scraper', 'Robot',
        'curl', 'wget', 'Scraper', 'MJ12bot', 'SEMrushBot', 'DotBot',
        'Exabot', 'Facebot', 'ia_archiver', 'Java', 'AhrefsBot',
        'SemrushBot', 'MJ12bot', 'MailRu', 'Uptimerobot'
    ];

    public function __construct(?array $headers = null, $userAgent = null)
    {
        $this->setHttpHeaders($headers);
        $this->setUserAgent($userAgent);
    }

    public function setHttpHeaders($httpHeaders = null)
    {
        if (!is_array($httpHeaders) || empty($httpHeaders)) {
            $httpHeaders = $_SERVER;
        }

        $this->httpHeaders = [];
        foreach ($httpHeaders as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $this->httpHeaders[$key] = $value;
            }
        }
    }

    public function setUserAgent($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = '';
            $uaHeaders = ['HTTP_USER_AGENT', 'HTTP_X_SCANNER', 'HTTP_X_AUTOMATED_TOOL'];
            foreach ($uaHeaders as $header) {
                if (isset($this->httpHeaders[$header])) {
                    $userAgent .= $this->httpHeaders[$header] . ' ';
                }
            }
            if ($userAgent === '') {
                $userAgent = null;
            }
        }
        return $this->userAgent = $userAgent;
    }

    public function isCrawler($userAgent = null)
    {
        $this->matches = [];
        $agent = $userAgent ?: $this->userAgent;

        if (empty($agent)) {
            return false;
        }

        foreach (self::$crawlers as $crawler) {
            if (stripos($agent, $crawler) !== false) {
                $this->matches[0] = $crawler;
                return true;
            }
        }

        return false;
    }

    public function getMatches()
    {
        return isset($this->matches[0]) ? $this->matches[0] : null;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }
}
?>