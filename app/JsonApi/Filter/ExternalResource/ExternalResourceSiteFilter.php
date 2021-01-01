<?php

namespace App\JsonApi\Filter\ExternalResource;

use App\Enums\ResourceSite;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

class ExternalResourceSiteFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'site', ResourceSite::class);
    }
}
