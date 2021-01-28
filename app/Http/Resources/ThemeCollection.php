<?php

namespace App\Http\Resources;

use App\JsonApi\Filter\Theme\ThemeGroupFilter;
use App\JsonApi\Filter\Theme\ThemeSequenceFilter;
use App\JsonApi\Filter\Theme\ThemeTypeFilter;
use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\Concerns\JsonApi\PerformsResourceCollectionSearch;

class ThemeCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery, PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'themes';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($theme) {
            return ThemeResource::make($theme, $this->parser);
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
            'anime.images',
            'entries',
            'entries.videos',
            'song',
            'song.artists',
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
            'theme_id',
            'created_at',
            'updated_at',
            'group',
            'type',
            'sequence',
            'slug',
            'anime_id',
            'song_id',
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
            ThemeGroupFilter::class,
            ThemeSequenceFilter::class,
            ThemeTypeFilter::class,
        ];
    }
}
