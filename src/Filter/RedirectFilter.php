<?php

namespace Elfennol\SitemapGenerator\Filter;

use Elfennol\SitemapGenerator\Tags;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RedirectFilter implements FilterInterface
{
    private array $params = [];

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function filter(array $urls): FilterResult
    {
        if (isset($this->params['cookie'])) {
            $this->httpClient = $this->httpClient->withOptions([
                'headers' => [
                    'Cookie' => $this->params['cookie'],
                ],
            ]);
        }

        $filterResult = new FilterResult();
        foreach ($urls as $url) {
            try {
                $this->httpClient->request('GET', $url[Tags::LOC], ['max_redirects' => 0])->getContent();
                $filterResult->addKo($url);
            } catch (RedirectionException $redirectionException) {
                $url[Tags::LOC] = $redirectionException->getResponse()->getInfo()['redirect_url'];
                $filterResult->addOk($url);
            }
        }

        return $filterResult;
    }

    public function getFilter(): Filters
    {
        return Filters::REDIRECT;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}
