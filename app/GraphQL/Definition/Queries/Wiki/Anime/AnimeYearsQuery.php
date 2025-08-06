<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Controllers\Wiki\Anime\AnimeYearsController;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYearType;
use App\GraphQL\Support\Argument\Argument;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;

#[UseFieldDirective(AnimeYearsController::class, 'index')]
class AnimeYearsQuery extends BaseQuery
{
    final public const ARGUMENT_YEAR = 'year';
    final public const ARGUMENT_SEASON = 'season';

    public function __construct()
    {
        parent::__construct('animeyears');
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
        $season = app(TypeRegistry::class)->get(class_basename(AnimeSeason::class));

        return [
            new Argument(self::ARGUMENT_YEAR, Type::listOf(Type::nonNull(Type::int()))),

            new Argument(self::ARGUMENT_SEASON, Type::listOf(Type::nonNull($season))),
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
