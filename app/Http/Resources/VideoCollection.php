<?php

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\Concerns\JsonApi\PerformsResourceCollectionSearch;
use App\JsonApi\Filter\Video\VideoLyricsFilter;
use App\JsonApi\Filter\Video\VideoNcFilter;
use App\JsonApi\Filter\Video\VideoOverlapFilter;
use App\JsonApi\Filter\Video\VideoResolutionFilter;
use App\JsonApi\Filter\Video\VideoSourceFilter;
use App\JsonApi\Filter\Video\VideoSubbedFilter;
use App\JsonApi\Filter\Video\VideoUncenFilter;

class VideoCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery, PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'videos';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($video) {
            return VideoResource::make($video, $this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
     */
    public static function allowedIncludePaths()
    {
        return [
            'entries',
            'entries.theme',
            'entries.theme.anime',
        ];
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return array
     */
    public static function allowedSortFields()
    {
        return [
            'video_id',
            'created_at',
            'updated_at',
            'filename',
            'path',
            'size',
            'basename',
            'resolution',
            'nc',
            'subbed',
            'lyrics',
            'uncen',
            'source',
            'overlap',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return array
     */
    public static function filters()
    {
        return [
            VideoLyricsFilter::class,
            VideoNcFilter::class,
            VideoOverlapFilter::class,
            VideoResolutionFilter::class,
            VideoSourceFilter::class,
            VideoSubbedFilter::class,
            VideoUncenFilter::class,
        ];
    }
}
