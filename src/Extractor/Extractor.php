<?php

namespace Elfennol\SitemapGenerator\Extractor;

use DOMDocument;
use DOMNode;
use Elfennol\SitemapGenerator\Tags;
use RuntimeException;
use SplFileObject;

class Extractor
{
    private const FORMAT_TXT = 'txt';
    private const FORMAT_JSON = 'json';
    private const FORMAT_XML = 'xml';

    public function extract(string $source): array
    {
        $format = pathinfo($source, PATHINFO_EXTENSION);

        return match ($format) {
            self::FORMAT_TXT => $this->txt($source),
            self::FORMAT_JSON => $this->json($source),
            self::FORMAT_XML => $this->xml($source),
            default => throw new RuntimeException('Invalid format.')
        };
    }

    private function txt(string $source): array
    {
        $file = new SplFileObject($source);
        $urls = [];

        while (!$file->eof()) {
            $url = trim($file->fgets());
            if ($url) {
                $urls[] = [Tags::LOC => $url];
            }
        }

        $file = null;

        return $urls;
    }

    private function json(string $source): array
    {
        return json_decode(
            json: file_get_contents($source),
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );
    }

    private function xml(string $source): array
    {
        $urls = [];
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML(file_get_contents($source));
        foreach ($dom->getElementsByTagName('url') as $node) {
            $url = [];
            $this->assertNodeListIsNotEmpty($node, Tags::LOC);
            foreach (Tags::URL_CHILDREN as $tagName) {
                $this->processNodeList($node, $tagName, $url);
            }
            $urls[] = $url;
        }

        return $urls;
    }

    private function processNodeList(DOMNode $node, string $tagName, array &$url): void
    {
        $element = $node->getElementsByTagName($tagName);
        if ($element->length > 0) {
            $url[$tagName] = $element->item(0)->nodeValue;
        }
    }

    private function assertNodeListIsNotEmpty(DOMNode $node, string $tagName): void
    {
        if (!($node->getElementsByTagName($tagName)->length > 0)) {
            throw new RuntimeException('NodeList is empty.');
        }
    }
}
