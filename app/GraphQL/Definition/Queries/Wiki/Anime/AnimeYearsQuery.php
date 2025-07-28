<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Resolvers\AnimeYearResolver;
use App\GraphQL\Support\Argument;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\Type;

#[UseFieldDirective(AnimeYearResolver::class, 'years')]
class AnimeYearsQuery extends BaseQuery
{
    public function __construct()
    {
        parent::__construct('animeyears', false, false);
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a list of unique years from all anime resources.';
    }

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            ...parent::directives(),

            'canModel' => [
                'ability' => 'viewAny',
                'injectArgs' => 'true',
                'model' => Anime::class,
            ],
        ];
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): Type
    {
        return Type::listOf(Type::nonNull(Type::int()));
    }
}
