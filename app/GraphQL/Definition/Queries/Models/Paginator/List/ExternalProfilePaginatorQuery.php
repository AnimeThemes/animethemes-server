<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\List;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\List\ExternalProfileType;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;

class ExternalProfilePaginatorQuery extends EloquentPaginatorQuery
{
    protected $middleware = [
        EnabledOnlyOnLocalhost::class,
    ];

    public function __construct()
    {
        parent::__construct('externalprofilePaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of external profile resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): ExternalProfileType
    {
        return new ExternalProfileType();
    }
}
