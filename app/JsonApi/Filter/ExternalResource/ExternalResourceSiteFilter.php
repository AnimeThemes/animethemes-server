<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\ExternalResource;

use App\Enums\ResourceSite;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

/**
 * Class ExternalResourceSiteFilter.
 */
class ExternalResourceSiteFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'site', ResourceSite::class);
    }
}
