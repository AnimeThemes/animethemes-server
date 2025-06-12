<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\StudioType;

/**
 * Class StudiosQuery.
 */
class StudiosQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('studios');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of studio resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return array<int, string>
     */
    public function arguments(): array
    {
        return [
            'search: String @search',

            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "StudioColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return StudioType
     */
    public function baseType(): StudioType
    {
        return new StudioType();
    }
}
