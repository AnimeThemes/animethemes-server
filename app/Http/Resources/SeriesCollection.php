<?php

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\Concerns\JsonApi\PerformsResourceCollectionSearch;

class SeriesCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery, PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'series';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($series) {
            return SeriesResource::make($series, $this->parser);
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
            'anime',
            'anime.synonyms',
            'anime.themes',
            'anime.themes.entries',
            'anime.themes.entries.videos',
            'anime.themes.song',
            'anime.themes.song.artists',
            'anime.externalResources',
            'anime.images',
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
            'series_id',
            'created_at',
            'updated_at',
            'slug',
            'name',
        ];
    }
}
