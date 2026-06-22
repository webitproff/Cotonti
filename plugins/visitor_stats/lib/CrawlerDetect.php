<?php

namespace Jaybizzle\CrawlerDetect;

use Jaybizzle\CrawlerDetect\Fixtures\AbstractProvider;
use Jaybizzle\CrawlerDetect\Fixtures\Crawlers;
use Jaybizzle\CrawlerDetect\Fixtures\Exclusions;
use Jaybizzle\CrawlerDetect\Fixtures\Headers;

class CrawlerDetect
{
    protected $userAgent;
    protected $httpHeaders = [];
    protected $matches = [];
    protected $crawlers;
    protected $exclusions;
    protected $uaHttpHeaders;
    protected $compiledRegex;
    protected $compiledExclusions;
    protected static $compileCache = [];

    public function __construct(?array $headers = null, $userAgent = null)
    {
        $this->crawlers = new Crawlers();
        $this->exclusions = new Exclusions();
        $this->uaHttpHeaders = new Headers();

        $this->compiledRegex = $this->compileFixtureRegex($this->crawlers);
        $this->compiledExclusions = $this->compileFixtureRegex($this->exclusions);

        $this->setHttpHeaders($headers);
        $this->setUserAgent($userAgent);
    }

    protected function compileFixtureRegex(AbstractProvider $fixture)
    {
        $class = get_class($fixture);

        if (!isset(self::$compileCache[$class])) {
            self::$compileCache[$class] = $this->compileRegex($fixture->getAll());
        }

        return self::$compileCache[$class];
    }

    public function compileRegex($patterns)
    {
        return '(?:' . implode('|', $patterns) . ')';
    }

    public function setHttpHeaders($httpHeaders = null)
    {
        if (!is_array($httpHeaders) || !count($httpHeaders)) {
            $httpHeaders = $_SERVER;
        }

        $this->httpHeaders = [];

        foreach ($httpHeaders as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $this->httpHeaders[$key] = $value;
            }
        }
    }

    public function getUaHttpHeaders()
    {
        return $this->uaHttpHeaders->getAll();
    }

    public function setUserAgent($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = '';

            foreach ($this->getUaHttpHeaders() as $altHeader) {
                if (isset($this->httpHeaders[$altHeader])) {
                    $userAgent .= $this->httpHeaders[$altHeader] . ' ';
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

        $agent = preg_replace(
            '/' . $this->compiledExclusions . '/i',
            '',
            $userAgent ?: $this->userAgent ?: ''
        );

        if ($agent === null || trim($agent) === '') {
            return false;
        }

        $agent = trim($agent);

        $result = preg_match('/' . $this->compiledRegex . '/i', $agent, $this->matches);

        if ($result === false) {
            $this->matches = [];
            return false;
        }

        return (bool)$result;
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
