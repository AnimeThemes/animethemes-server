<?php

namespace App\Http\Resources;

use App\JsonApi\Filter\Anime\AnimeSeasonFilter;
use App\JsonApi\Filter\Anime\AnimeYearFilter;
use App\JsonApi\Traits\PerformsResourceCollectionQuery;
use App\JsonApi\Traits\PerformsResourceCollectionSearch;

class AnimeCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery, PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'anime';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($anime) {
            return AnimeResource::make($anime, $this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @var array
     */
    public static function allowedIncludePaths()
    {
        return [
            'synonyms',
            'series',
            'themes',
            'themes.entries',
            'themes.entries.videos',
            'themes.song',
            'themes.song.artists',
            'externalResources',
            'images',
        ];
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static function allowedSortFields()
    {
        return [
            'anime_id',
            'created_at',
            'updated_at',
            'slug',
            'name',
            'year',
            'season',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @var array
     */
    public static function filters()
    {
        return [
            AnimeSeasonFilter::class,
            AnimeYearFilter::class,
        ];
    }
}
