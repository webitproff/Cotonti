<?php

namespace Jaybizzle\CrawlerDetect\Fixtures;

class Exclusions extends AbstractProvider
{
    protected $data = [
        'Safari.*',
        'Firefox.*',
        ' Chrome.*',
        'Chromium.*',
        'MSIE.*',
        'Opera/.*',
        'Mozilla.*',
        'AppleWebKit.*',
        'Trident.*',
        'Windows NT.*',
        'Android.*',
        'Macintosh',
        'Ubuntu',
        'Linux x86_64',
        'Mac OS X.*',
        'Gecko',
        'KHTML',
        'CriOS.*',
        'CPU iPhone',
        'CPU OS.*Mac',
        'iPod',
        'x64',
        'X11'
    ];
}
