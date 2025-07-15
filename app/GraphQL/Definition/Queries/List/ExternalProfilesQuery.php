<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\List;

use App\GraphQL\Attributes\UseBuilder;
use App\GraphQL\Builders\List\ExternalProfileBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\List\ExternalProfileType;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;

/**
 * Class ExternalProfilesQuery.
 */
#[UseBuilder(ExternalProfileBuilder::class)]
class ExternalProfilesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('externalprofiles');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of external profile resources given fields.';
    }

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            'middleware' => [
                'class' => EnabledOnlyOnLocalhost::class,
            ],

            ...parent::directives(),
        ];
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

            'orderBy: _ @orderBy(columnsEnum: "ExternalProfileColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return ExternalProfileType
     */
    public function baseType(): ExternalProfileType
    {
        return new ExternalProfileType();
    }
}
