<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\GraphQL\Attributes\UseField;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYearType;
use App\GraphQL\Queries\AnimeYear;
use App\Models\Wiki\Anime;

/**
 * Class AnimeYearQuery.
 */
#[UseField(AnimeYear::class, 'year')]
class AnimeYearQuery extends BaseQuery
{
    public function __construct()
    {
        parent::__construct('animeyear', false, false, false);
    }

    /**
     * The description of the type.
     *
     * @return string
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
     * @return string[]
     */
    public function arguments(): array
    {
        return [
            'year: Int!',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return AnimeYearType
     */
    public function baseType(): AnimeYearType
    {
        return new AnimeYearType();
    }
}
