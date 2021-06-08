<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Base;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\QueryParser;

/**
 * Class TrashedFilter.
 */
class TrashedFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'trashed', TrashedStatus::class);
    }
}
