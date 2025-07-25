<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;

#[UsePaginateDirective]
class ExternalResourcesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('externalresources');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): ExternalResourceType
    {
        return new ExternalResourceType();
    }
}
