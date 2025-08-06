<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason\AnimeYearSeasonSeasonField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeasonsField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearYearField;
use App\GraphQL\Definition\Queries\Wiki\Anime\AnimeYearsQuery;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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
        $season = Arr::get($args, AnimeYearsQuery::ARGUMENT_SEASON);
        $year = Arr::get($args, AnimeYearsQuery::ARGUMENT_YEAR);

        if (($year === null || count($year) > 1) && Arr::get($resolveInfo->getFieldSelection(1), 'seasons.animes')) {
            throw new ClientValidationException("Please provide a unique 'year' argument to query the animes field.");
        }

        return Anime::query()
            ->distinct(Anime::ATTRIBUTE_YEAR)
            ->orderBy(Anime::ATTRIBUTE_YEAR)
            ->when($season !== null, fn (Builder $query) => $query->whereIn(Anime::ATTRIBUTE_SEASON, $season))
            ->when($year !== null, fn (Builder $query) => $query->whereIn(Anime::ATTRIBUTE_YEAR, $year))
            ->get()
            ->groupBy(Anime::ATTRIBUTE_YEAR)
            ->map(function (Collection $animesByYear, $yearNested) {
                return [
                    AnimeYearYearField::FIELD => $yearNested,
                    AnimeYearSeasonsField::FIELD => $animesByYear->groupBy(Anime::ATTRIBUTE_SEASON)->map(function ($collection, int $seasonNested) use ($yearNested) {
                        $seasonNested = AnimeSeason::from($seasonNested);

                        return [
                            AnimeYearSeasonSeasonField::FIELD => $seasonNested,
                            'seasonLocalized' => $seasonNested->localize(),
                            'year' => $yearNested,
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Apply the builder for the AnimeYearSeasonAnimesField.
     */
    public function applyBuilder(array $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        return Anime::query()
            ->where(Anime::ATTRIBUTE_SEASON, Arr::get($root, AnimeYearSeasonSeasonField::FIELD)->value)
            ->where(Anime::ATTRIBUTE_YEAR, Arr::get($root, 'year'));
    }
}
