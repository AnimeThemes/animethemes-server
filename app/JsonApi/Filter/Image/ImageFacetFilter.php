<?php

namespace App\JsonApi\Filter\Image;

use App\Enums\ImageFacet;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

class ImageFacetFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'facet', ImageFacet::class);
    }
}
