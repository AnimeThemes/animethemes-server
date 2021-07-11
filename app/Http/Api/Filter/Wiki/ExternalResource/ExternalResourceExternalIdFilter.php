<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\ExternalResource;

use App\Http\Api\Filter\IntFilter;
use App\Http\Api\QueryParser;

/**
 * Class ExternalResourceExternalIdFilter.
 */
class ExternalResourceExternalIdFilter extends IntFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'external_id');
    }
}
