<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Filter\EnumFilter;
use Illuminate\Support\Collection;

/**
 * Class ImageFacetFilter.
 */
class ImageFacetFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'facet', ImageFacet::class);
    }
}
