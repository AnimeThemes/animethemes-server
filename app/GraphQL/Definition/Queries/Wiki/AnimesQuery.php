<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Attributes\UseSearchDirective;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Resolvers\PaginateResolver;

#[UsePaginateDirective]
#[UseSearchDirective]
class AnimesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('animes');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of anime resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AnimeType
    {
        return new AnimeType();
    }

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            'paginate' => [
                'builder' => PaginateResolver::class,
            ],
        ];
    }
}
