<?php

namespace Elfennol\SitemapGenerator\Generator;

use DOMDocument;
use Elfennol\SitemapGenerator\Tags;
use RuntimeException;

readonly class SitemapGenerator
{
    public function generate(array $urls): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $xmlUrlset = $dom->createElement(Tags::URLSET);
        $xmlUrlset = $dom->appendChild($xmlUrlset);
        $xmlUrlsetAttr = $dom->createAttribute('xmlns');
        $xmlUrlsetAttr->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $xmlUrlset->appendChild($xmlUrlsetAttr);

        $processedUrls = [];
        foreach ($urls as $url) {
            $this->assertLocIsNotEmpty($url[Tags::LOC]);
            if (!isset($processedUrls[$url[Tags::LOC]])) {
                $xmlUrl = $dom->createElement(Tags::URL);
                $xmlLoc = $dom->createElement(Tags::LOC, $url[Tags::LOC]);
                $xmlUrl->appendChild($xmlLoc);
                if (isset($url[Tags::LASTMOD])) {
                    $xmlLastmod = $dom->createElement(Tags::LASTMOD, $url[Tags::LASTMOD]);
                    $xmlUrl->appendChild($xmlLastmod);
                }
                if (isset($url[Tags::CHANGEFREQ])) {
                    $this->assertIsChangefreq($url[Tags::CHANGEFREQ]);
                    $xmlChangefreq = $dom->createElement(Tags::CHANGEFREQ, $url[Tags::CHANGEFREQ]);
                    $xmlUrl->appendChild($xmlChangefreq);
                }
                if (isset($url[Tags::PRIORITY])) {
                    $this->assertIsPriority($url[Tags::PRIORITY]);
                    $xmlPriority = $dom->createElement(Tags::PRIORITY, $url[Tags::PRIORITY]);
                    $xmlUrl->appendChild($xmlPriority);
                }
                $xmlUrlset->appendChild($xmlUrl);
                $processedUrls[$url[Tags::LOC]] = true;
            }
        }

        $dom->formatOutput = true;

        return $dom->saveXML();
    }

    private function assertLocIsNotEmpty($value): void
    {
        if (!$value) {
            throw new RuntimeException('Loc is empty.');
        }
    }

    private function assertIsChangefreq($value): void
    {
        if (!in_array(
            $value,
            [
                'always',
                'hourly',
                'daily',
                'weekly',
                'monthly',
                'yearly',
                'never',
            ],
            true
        )) {
            throw new RuntimeException('Value is not a changefreq.');
        }
    }

    private function assertIsPriority($value): void
    {
        if (!((float)$value >= 0 && (float)$value <= 1)) {
            throw new RuntimeException('Value is not a priority.');
        }
    }
}
