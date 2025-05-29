<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Document;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Document\PageType;
use GraphQL\Type\Definition\Type;

/**
 * Class PagesQuery.
 */
class PagesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('pages');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of page resources given fields.';
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

            'orderBy: _ @orderBy(columnsEnum: "PageColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return Type
     */
    public function baseType(): Type
    {
        return new PageType();
    }
}
