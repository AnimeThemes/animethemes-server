<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin;

use App\GraphQL\Builders\Admin\DumpBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Admin\DumpType;
use GraphQL\Type\Definition\Type;

/**
 * Class DumpsQuery.
 */
class DumpsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('dumps');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of dump resources given fields.';
    }

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            'builder' => [
                'method' => DumpBuilder::class.'@index',
            ],

            ...parent::directives(),
        ];
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

            'orderBy: _ @orderBy(columnsEnum: "DumpColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return Type
     */
    public function baseType(): Type
    {
        return new DumpType();
    }
}
