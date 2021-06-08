<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\ExternalResource;

use App\Enums\ResourceSite;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\QueryParser;

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
