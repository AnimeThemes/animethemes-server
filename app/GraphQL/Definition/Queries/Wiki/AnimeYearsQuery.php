<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Controllers\Wiki\Anime\AnimeYearsController;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYearType;
use App\GraphQL\Support\Argument\Argument;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\Type;

#[UseFieldDirective(AnimeYearsController::class, 'index')]
class AnimeYearsQuery extends BaseQuery
{
    final public const ARGUMENT_YEAR = 'year';

    public function __construct()
    {
        parent::__construct('animeyears');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a list of years grouped by its seasons.';
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
            new Argument(self::ARGUMENT_YEAR, Type::listOf(Type::nonNull(Type::int()))),
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): Type
    {
        return new AnimeYearType();
    }

    /**
     * The type returned by the field.
     */
    public function getType(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull($this->baseType())));
    }
}
