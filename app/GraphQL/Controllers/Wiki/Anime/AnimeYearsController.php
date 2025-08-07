<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason\AnimeYearSeasonSeasonField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeasonField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeasonsField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearYearField;
use App\GraphQL\Definition\Queries\Wiki\Anime\AnimeYearsQuery;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * @extends BaseController<Anime>
 */
class AnimeYearsController extends BaseController
{
    /**
     * Apply the builder to the animeyears query.
     *
     * @param  array<string, mixed>  $args
     */
    public function index(null $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        $year = Arr::get($args, AnimeYearsQuery::ARGUMENT_YEAR);

        $fieldSelection = $resolveInfo->getFieldSelection(1);

        if (
            ($year === null || count($year) > 1)
            && (Arr::get($fieldSelection, 'season.animes') || Arr::get($fieldSelection, 'seasons.animes'))
        ) {
            throw new ClientValidationException("Please provide a unique 'year' argument to query the animes field.");
        }

        return Anime::query()
            ->distinct([Anime::ATTRIBUTE_YEAR, Anime::ATTRIBUTE_SEASON])
            ->orderBy(Anime::ATTRIBUTE_YEAR)
            ->when($year !== null, fn (Builder $query) => $query->whereIn(Anime::ATTRIBUTE_YEAR, $year))
            ->get([Anime::ATTRIBUTE_YEAR, Anime::ATTRIBUTE_SEASON])
            ->groupBy(Anime::ATTRIBUTE_YEAR)
            ->map(function (Collection $items, int $year) {
                return [
                    AnimeYearYearField::FIELD => $year,

                    AnimeYearSeasonsField::FIELD => $items
                        ->map(function (Anime $anime) use ($year) {
                            return [
                                'year' => $year,
                                AnimeYearSeasonSeasonField::FIELD => $anime->season,
                                'seasonLocalized' => $anime->season->localize(),
                            ];
                        })
                        ->unique(Anime::ATTRIBUTE_SEASON)
                        ->values()
                        ->toArray(),
                ];
            })->values();
    }

    /**
     * Apply the resolver to the AnimeYearSeasonField.
     *
     * @param  array<string, mixed>  $args
     */
    public function applyFieldToSeasonField(array $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        $season = AnimeSeason::from(Arr::get($args, AnimeYearSeasonField::ARGUMENT_SEASON));
        $year = Arr::get($root, AnimeYearsQuery::ARGUMENT_YEAR);

        $seasons = collect(Arr::get($root, 'seasons'));

        if ($seasons->doesntContain(fn ($item) => $item[AnimeYearSeasonSeasonField::FIELD] === $season)) {
            return null;
        }

        return [
            AnimeYearSeasonSeasonField::FIELD => $season,
            'seasonLocalized' => $season->localize(),
            'year' => $year,
        ];
    }

    /**
     * Apply the builder for the AnimeYearSeasonAnimesField.
     */
    public function applyBuilderToAnimesField(array $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        $season = Arr::get($root, AnimeYearSeasonSeasonField::FIELD);
        $year = Arr::get($root, 'year');

        return Anime::query()
            ->when($season !== null, fn (Builder $query) => $query->where(Anime::ATTRIBUTE_SEASON, $season->value))
            ->where(Anime::ATTRIBUTE_YEAR, $year);
    }
}
