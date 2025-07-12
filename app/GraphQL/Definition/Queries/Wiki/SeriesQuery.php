<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\SeriesType;

/**
 * Class SeriesQuery.
 */
class SeriesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('series');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of series resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        return [
            'search: String @search',

            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "SeriesColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return SeriesType
     */
    public function baseType(): SeriesType
    {
        return new SeriesType();
    }
}
