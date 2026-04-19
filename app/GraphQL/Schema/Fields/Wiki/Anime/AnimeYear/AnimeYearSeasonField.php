<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Enums\Models\Wiki\AnimeSeason;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason\AnimeYearSeasonSeasonField;
use App\GraphQL\Schema\Queries\Wiki\AnimeYearsQuery;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeYear\AnimeYearSeasonType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;

class AnimeYearSeasonField extends Field implements DisplayableField
{
    final public const string FIELD = 'season';
    final public const string ARGUMENT_SEASON = 'season';

    public function __construct()
    {
        parent::__construct(self::FIELD);
    }

    public function description(): string
    {
        return 'Object that references the season year queried';
    }

    public function baseType(): AnimeYearSeasonType
    {
        return new AnimeYearSeasonType();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function args(): array
    {
        return [
            self::ARGUMENT_SEASON => [
                'type' => Type::nonNull(GraphQL::type(class_basename(AnimeSeason::class))),
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        $season = Arr::get($args, self::ARGUMENT_SEASON);
        $year = Arr::get($root, AnimeYearsQuery::ARGUMENT_YEAR);

        $seasons = collect(Arr::get($root, 'seasons'));

        if ($seasons->doesntContain(fn ($item): bool => $item[AnimeYearSeasonSeasonField::FIELD] === $season)) {
            return null;
        }

        return [
            AnimeYearSeasonSeasonField::FIELD => $season,
            'seasonLocalized' => $season->localize(),
            'year' => $year, // Needed to query animes on the 'season' field.
        ];
    }
}
