<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\List;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Attributes\UseSearchDirective;
use App\GraphQL\Controllers\List\ExternalProfileController;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\List\ExternalProfileType;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;

#[UseBuilderDirective(ExternalProfileController::class)]
#[UsePaginateDirective]
#[UseSearchDirective]
class ExternalProfilePaginatorQuery extends EloquentPaginatorQuery
{
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
     * The base return type of the query.
     */
    public function baseType(): ExternalProfileType
    {
        return new ExternalProfileType();
    }
}
