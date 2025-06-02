<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Song;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\Song\PerformanceType;
use GraphQL\Type\Definition\Type;

/**
 * Class PerformancesQuery.
 */
class PerformancesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('performances');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of performances resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return array<int, string>
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "PerformanceColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return Type
     */
    public function baseType(): Type
    {
        return new PerformanceType();
    }
}
