<?php

namespace App\Http\Resources;

use App\JsonApi\Filter\Entry\EntryNsfwFilter;
use App\JsonApi\Filter\Entry\EntrySpoilerFilter;
use App\JsonApi\Filter\Entry\EntryVersionFilter;
use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\Concerns\JsonApi\PerformsResourceCollectionSearch;

class EntryCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery, PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'entries';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($entry) {
            return EntryResource::make($entry, $this->parser);
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
            'theme',
            'videos',
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
            'entry_id',
            'created_at',
            'updated_at',
            'version',
            'nsfw',
            'spoiler',
            'theme_id',
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
            EntryNsfwFilter::class,
            EntrySpoilerFilter::class,
            EntryVersionFilter::class,
        ];
    }
}
