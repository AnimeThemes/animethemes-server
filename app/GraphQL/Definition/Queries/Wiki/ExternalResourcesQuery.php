<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;

class ExternalResourcesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('externalresources');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "ExternalResourceColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return ExternalResourceType
     */
    public function baseType(): ExternalResourceType
    {
        return new ExternalResourceType();
    }
}
