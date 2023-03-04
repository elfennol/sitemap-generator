<?php

namespace Elfennol\SitemapGenerator\Filter;

interface FilterInterface
{
    public function filter(array $urls): FilterResult;

    public function getFilter(): Filters;

    public function setParams(array $params): void;
}
