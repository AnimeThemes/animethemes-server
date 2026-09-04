<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class AnimeThemeShuffleQuery
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(null $_, array $args): Collection
    {
        $builder = Theme::query();

        if (is_array($types = Arr::get($args, 'type'))) {
            $builder->whereIn(Theme::ATTRIBUTE_TYPE, $types);
        }

        $builder->whereHas(Theme::RELATION_ANIME, function (Builder $query) use ($args): void {
            if (is_array($formats = Arr::get($args, 'format'))) {
                $query->whereIn(Anime::ATTRIBUTE_FORMAT, $formats);
            }

            if (is_int($yearLte = Arr::get($args, 'year_lte'))) {
                $query->where(Anime::ATTRIBUTE_YEAR, ComparisonOperator::LTE->value, $yearLte);
            }

            if (is_int($yearGte = Arr::get($args, 'year_gte'))) {
                $query->where(Anime::ATTRIBUTE_YEAR, ComparisonOperator::GTE->value, $yearGte);
            }
        });

        $builder->whereHas(Theme::RELATION_VIDEOS);

        if (is_bool($spoiler = Arr::get($args, 'spoiler'))) {
            $builder->whereRelation(Theme::RELATION_ENTRIES, Entry::ATTRIBUTE_SPOILER, $spoiler);
        }

        $builder->inRandomOrder();

        $first = Arr::get($args, 'first') ?? Config::integer('lighthouse.pagination.default_count');
        $page = Arr::integer($args, 'page', 1);

        return $builder->paginate($first, page: $page)->getCollection();
    }
}
