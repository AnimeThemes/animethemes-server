<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Attributes\UseSearchDirective;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\StudioType;

#[UsePaginateDirective]
#[UseSearchDirective]
class StudioPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('studioPaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of studio resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): StudioType
    {
        return new StudioType();
    }
}
