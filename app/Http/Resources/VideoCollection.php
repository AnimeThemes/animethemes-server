<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\Concerns\JsonApi\PerformsResourceCollectionSearch;
use App\JsonApi\Filter\Base\CreatedAtFilter;
use App\JsonApi\Filter\Base\DeletedAtFilter;
use App\JsonApi\Filter\Base\TrashedFilter;
use App\JsonApi\Filter\Base\UpdatedAtFilter;
use App\JsonApi\Filter\Video\VideoLyricsFilter;
use App\JsonApi\Filter\Video\VideoNcFilter;
use App\JsonApi\Filter\Video\VideoOverlapFilter;
use App\JsonApi\Filter\Video\VideoResolutionFilter;
use App\JsonApi\Filter\Video\VideoSourceFilter;
use App\JsonApi\Filter\Video\VideoSubbedFilter;
use App\JsonApi\Filter\Video\VideoUncenFilter;
use Illuminate\Http\Request;

/**
 * Class VideoCollection.
 */
class VideoCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery;
    use PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'videos';

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (VideoResource $resource) {
            return $resource->parser($this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
     */
    public static function allowedIncludePaths(): array
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
    public static function allowedSortFields(): array
    {
        return [
            'video_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'filename',
            'path',
            'size',
            'mimetype',
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
    public static function filters(): array
    {
        return [
            VideoLyricsFilter::class,
            VideoNcFilter::class,
            VideoOverlapFilter::class,
            VideoResolutionFilter::class,
            VideoSourceFilter::class,
            VideoSubbedFilter::class,
            VideoUncenFilter::class,
            CreatedAtFilter::class,
            UpdatedAtFilter::class,
            DeletedAtFilter::class,
            TrashedFilter::class,
        ];
    }
}
