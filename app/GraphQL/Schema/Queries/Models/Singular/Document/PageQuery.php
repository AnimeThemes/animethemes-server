<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Singular\Document;

use App\GraphQL\Schema\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Schema\Types\Document\PageType;

class PageQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('page');
    }

    public function description(): string
    {
        return 'Returns a page resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PageType
    {
        return new PageType();
    }
}
