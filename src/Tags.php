<?php

namespace Elfennol\SitemapGenerator;

class Tags
{
    public const URLSET = 'urlset';
    public const URL = 'url';
    public const LOC = 'loc';
    public const LASTMOD = 'lastmod';
    public const CHANGEFREQ = 'changefreq';
    public const PRIORITY = 'priority';

    public const URL_CHILDREN = [self::LOC, self::LASTMOD, self::CHANGEFREQ, self::PRIORITY];
}
