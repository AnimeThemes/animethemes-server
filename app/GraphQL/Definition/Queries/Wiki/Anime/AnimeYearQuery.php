<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYearType;
use App\GraphQL\Resolvers\AnimeYearResolver;
use App\GraphQL\Support\Argument\Argument;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\Type;

#[UseFieldDirective(AnimeYearResolver::class, 'year')]
class AnimeYearQuery extends BaseQuery
{
    public function __construct()
    {
        parent::__construct('animeyear', false, false);
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of anime resources for a given year grouped by season and ordered by name.';
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
        return [
            new Argument('year', Type::int())
                ->required(),
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AnimeYearType
    {
        return new AnimeYearType();
    }
}
