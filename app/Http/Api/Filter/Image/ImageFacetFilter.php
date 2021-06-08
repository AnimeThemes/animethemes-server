<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Image;

use App\Enums\ImageFacet;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\QueryParser;

/**
 * Class ImageFacetFilter.
 */
class ImageFacetFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'facet', ImageFacet::class);
    }
}
