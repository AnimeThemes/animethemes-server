<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Wiki\Anime;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Concerns\Actions\GraphQL\PaginatesModels;
use App\Concerns\Actions\GraphQL\SortsModels;
use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason\AnimeYearSeasonSeasonField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeasonField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeasonsField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearYearField;
use App\GraphQL\Definition\Queries\Wiki\AnimeYearsQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

/**
 * @extends BaseController<Anime>
 */
class AnimeYearsController extends BaseController
{
    use ConstrainsEagerLoads;
    use FiltersModels;
    use PaginatesModels;
    use SortsModels;

    /**
     * Apply the builder to the animeyears query.
     *
     * @param  array<string, mixed>  $args
     */
    public function index(null $root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        $year = Arr::get($args, AnimeYearsQuery::ARGUMENT_YEAR);

        $fieldSelection = $resolveInfo->getFieldSelection(1);

        // Restrict 'animes' field to a unique year.
        if (
            ($year === null || count($year) > 1)
            && (Arr::get($fieldSelection, 'season.anime') || Arr::get($fieldSelection, 'seasons.anime'))
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
                                AnimeYearSeasonSeasonField::FIELD => $anime->season,
                                'seasonLocalized' => $anime->season->localize(),
                                'year' => $year, // Needed to query animes on the 'seasons' field.
                            ];
                        })
                        ->unique(Anime::ATTRIBUTE_SEASON)
                        ->values()
                        ->toArray(),
                ];
            })->values();
    }

    /**
     * Resolve the AnimeYearSeasonField.
     *
     * @param  array<string, mixed>  $args
     */
    public function resolveSeasonField(array $root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        $season = Arr::get($args, AnimeYearSeasonField::ARGUMENT_SEASON);
        $year = Arr::get($root, AnimeYearsQuery::ARGUMENT_YEAR);

        $seasons = collect(Arr::get($root, 'seasons'));

        if ($seasons->doesntContain(fn ($item) => $item[AnimeYearSeasonSeasonField::FIELD] === $season)) {
            return null;
        }

        return [
            AnimeYearSeasonSeasonField::FIELD => $season,
            'seasonLocalized' => $season->localize(),
            'year' => $year, // Needed to query animes on the 'season' field.
        ];
    }

    /**
     * Resolve the AnimeYearSeasonAnimeField.
     */
    public function resolveAnimeField(array $root, array $args, $context, ResolveInfo $resolveInfo): Paginator
    {
        $season = Arr::get($root, AnimeYearSeasonSeasonField::FIELD);
        $year = Arr::get($root, 'year');

        $builder = Anime::query()
            // season filter applies only on the 'season' field.
            ->when($season !== null, fn (Builder $query) => $query->where(Anime::ATTRIBUTE_SEASON, $season->value))
            ->where(Anime::ATTRIBUTE_YEAR, $year);

        $this->filter($builder, $args, new AnimeType());

        $this->sort($builder, $args, new AnimeType());

        $this->constrainEagerLoads($builder, $resolveInfo, new AnimeType());

        return $this->paginate($builder, $args);
    }
}
