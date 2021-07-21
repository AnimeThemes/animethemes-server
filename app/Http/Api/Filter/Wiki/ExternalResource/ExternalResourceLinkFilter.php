<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\ExternalResource;

use App\Http\Api\Filter\StringFilter;
use App\Http\Api\QueryParser;

/**
 * Class ExternalResourceLinkFilter.
 */
class ExternalResourceLinkFilter extends StringFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'link');
    }
}
