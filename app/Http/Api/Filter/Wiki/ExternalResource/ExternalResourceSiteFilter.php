<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\ExternalResource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Filter\EnumFilter;
use Illuminate\Support\Collection;

/**
 * Class ExternalResourceSiteFilter.
 */
class ExternalResourceSiteFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'site', ResourceSite::class);
    }
}
