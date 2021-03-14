<?php

namespace App\JsonApi\Filter\Base;

use App\Enums\Filter\TrashedStatus;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

class TrashedFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'trashed', TrashedStatus::class);
    }
}
