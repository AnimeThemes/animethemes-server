<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Wiki\Anime;
use App\Rules\GraphQL\Resolver\AnimeYearRule;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AnimeYearsQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Collection
    {
        $years = Arr::wrap(Arr::get($args, 'year'));

        $fieldSelection = $resolveInfo->getFieldSelection(1);

        Validator::make(['year' => $years], ['year' => new AnimeYearRule($fieldSelection)])
            ->validate();

        return Anime::query()
            ->whereNotNull(Anime::ATTRIBUTE_START_DATE)
            ->whereNotNull(Anime::ATTRIBUTE_SEASON)
            ->when($years !== null, function (Builder $query) use ($years): void {
                $query->where(function (Builder $query) use ($years): void {
                    foreach ($years as $singleYear) {
                        $query->orWhereBetween(Anime::ATTRIBUTE_START_DATE, [
                            "{$singleYear}0000",
                            "{$singleYear}1231",
                        ]);
                    }
                });
            })
            ->get([Anime::ATTRIBUTE_START_DATE, Anime::ATTRIBUTE_SEASON])
            ->groupBy(fn (Anime $anime): ?int => $anime->start_date?->year)
            ->map(fn (Collection $items, int $year): array => [
                'year' => $year,

                'seasons' => $items
                    ->map(fn (Anime $anime): array => [
                        'season' => $anime->season,
                        'seasonLocalized' => $anime->season->localize(),
                        'year' => $year,
                    ])
                    ->unique('season')
                    ->values()
                    ->toArray(),
            ])
            ->values();
    }

    /** @param  array{}  $args */
    public function resolveSeasonField(array $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?array
    {
        $season = Arr::get($args, 'season');
        $year = Arr::integer($root, 'year');

        $seasons = collect(Arr::get($root, 'seasons'));

        if ($seasons->doesntContain(fn ($item): bool => $item['season'] === $season)) {
            return null;
        }

        return [
            'season' => $season,
            'seasonLocalized' => $season->localize(),
            'year' => $year, // Needed to query animes on the 'season' field.
        ];
    }

    /** @param  array{}  $args */
    public function resolveAnimeField(array $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Paginator
    {
        $season = Arr::get($root, 'season');
        $year = Arr::integer($root, 'year');

        $builder = Anime::query()
            // season filter applies only on the 'season' field.
            ->when($season !== null, fn (Builder $query) => $query->where(Anime::ATTRIBUTE_SEASON, $season->value))
            ->where(Anime::ATTRIBUTE_YEAR, $year);

        $resolveInfo->enhanceBuilder($builder, [], $root, $args, $context, $resolveInfo);

        $first = Arr::get($args, 'first') ?? Config::integer('lighthouse.pagination.default_count');
        $page = Arr::integer($args, 'page', 1);

        return $builder->paginate($first, page: $page);
    }
}
