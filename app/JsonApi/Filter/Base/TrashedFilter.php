<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\Base;

use App\Enums\Filter\TrashedStatus;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

/**
 * Class TrashedFilter
 * @package App\JsonApi\Filter\Base
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
