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
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Class Search.
 */
class Search
{
    /**
     * Return a value for the field.
     *
     * @param  null  $root  Always null, since this field has no parent.
     * @param  array  $args  The field arguments passed by the client.
     * @param  GraphQLContext  $context  Shared between all fields.
     * @param  ResolveInfo  $resolveInfo  Metadata for advanced query resolution.
     * @return mixed The result of resolving the field, matching what was promised in the schema.
     */
    public function __invoke(null $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        $result = [];
        $fields = $resolveInfo->getFieldSelection();

        $term = Arr::get($args, 'search');
        $page = Arr::get($args, 'page');
        $perPage = Arr::get($args, 'perPage');

        if (Arr::get($fields, 'anime')) {
            $result['anime'] = Anime::search($term)->paginate($perPage, $page)->items();
        }

        if (Arr::get($fields, 'artists')) {
            $result['artists'] = Artist::search($term)->paginate($perPage, $page)->items();
        }

        if (Arr::get($fields, 'animethemes')) {
            $result['animethemes'] = AnimeTheme::search($term)->paginate($perPage, $page)->items();
        }

        if (Arr::get($fields, 'playlists')) {
            $result['playlists'] = Playlist::search($term)->paginate($perPage, $page)->items();
        }

        if (Arr::get($fields, 'series')) {
            $result['series'] = Series::search($term)->paginate($perPage, $page)->items();
        }

        if (Arr::get($fields, 'songs')) {
            $result['songs'] = Song::search($term)->paginate($perPage, $page)->items();
        }

        if (Arr::get($fields, 'studios')) {
            $result['studios'] = Studio::search($term)->paginate($perPage, $page)->items();
        }

        if (Arr::get($fields, 'videos')) {
            $result['videos'] = Video::search($term)->paginate($perPage, $page)->items();
        }

        return $result;
    }
}
