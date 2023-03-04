<?php

namespace Elfennol\SitemapGenerator\Filter;

class FilterResult
{
    private array $ok = [];
    private array $ko = [];

    public function addOk(array $url): void
    {
        $this->ok[] = $url;
    }

    public function addKo(array $url): void
    {
        $this->ko[] = $url;
    }

    public function getOk(): array
    {
        return $this->ok;
    }

    public function getKo(): array
    {
        return $this->ko;
    }
}
