<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Scout\Criteria;
use App\Scout\Search;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Number;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SearchQuery
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(null $_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $result = [];
        $fields = $resolveInfo->getFieldSelection();

        if (Arr::get($fields, 'anime')) {
            $result['anime'] = $this->search(Anime::class, $args);
        }

        if (Arr::get($fields, 'artists')) {
            $result['artists'] = $this->search(Artist::class, $args);
        }

        if (Arr::get($fields, 'animethemes')) {
            $result['animethemes'] = $this->search(AnimeTheme::class, $args);
        }

        if (Arr::get($fields, 'playlists')) {
            $result['playlists'] = $this->search(Playlist::class, $args);
        }

        if (Arr::get($fields, 'series')) {
            $result['series'] = $this->search(Series::class, $args);
        }

        if (Arr::get($fields, 'songs')) {
            $result['songs'] = $this->search(Song::class, $args);
        }

        if (Arr::get($fields, 'studios')) {
            $result['studios'] = $this->search(Studio::class, $args);
        }

        if (Arr::get($fields, 'videos')) {
            $result['videos'] = $this->search(Video::class, $args);
        }

        return $result;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected function search(string $modelClass, array $args): Collection
    {
        $term = Arr::get($args, 'search');
        $page = Arr::get($args, 'page', 1);
        $first = Number::clamp(Arr::get($args, 'first', Config::integer('lighthouse.pagination.default_count')), 1, 15);

        $searchBuilder = Search::getSearch($modelClass, new Criteria($term))
            ->search(
                fn (Builder $builder): Builder => $builder,
                $first,
                $page,
            );

        return collect($searchBuilder->items());
    }
}
