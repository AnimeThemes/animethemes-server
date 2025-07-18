<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\GraphQL\Attributes\UseField;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Queries\AnimeYear;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\Type;

/**
 * Class AnimeYearsQuery.
 */
#[UseField(AnimeYear::class, 'years')]
class AnimeYearsQuery extends BaseQuery
{
    public function __construct()
    {
        parent::__construct('animeyears', false, false, false);
    }

    /**
     * The description of the type.
     *
     * @return string
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
     * @return string[]
     */
    public function arguments(): array
    {
        return [];
    }

    /**
     * The base return type of the query.
     *
     * @return Type
     */
    public function baseType(): Type
    {
        return Type::listOf(Type::nonNull(Type::int()));
    }
}
