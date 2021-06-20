<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Concerns\Http\Resources\PerformsResourceCollectionSearch;
use App\Http\Api\Filter\Base\CreatedAtFilter;
use App\Http\Api\Filter\Base\DeletedAtFilter;
use App\Http\Api\Filter\Base\TrashedFilter;
use App\Http\Api\Filter\Base\UpdatedAtFilter;
use App\Http\Api\Filter\Wiki\Video\VideoLyricsFilter;
use App\Http\Api\Filter\Wiki\Video\VideoNcFilter;
use App\Http\Api\Filter\Wiki\Video\VideoOverlapFilter;
use App\Http\Api\Filter\Wiki\Video\VideoResolutionFilter;
use App\Http\Api\Filter\Wiki\Video\VideoSourceFilter;
use App\Http\Api\Filter\Wiki\Video\VideoSubbedFilter;
use App\Http\Api\Filter\Wiki\Video\VideoUncenFilter;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;

/**
 * Class VideoCollection.
 */
class VideoCollection extends BaseCollection
{
    use PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'videos';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Video::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Video $video) {
            return VideoResource::make($video, $this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
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
     * @return string[]
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
     * @return string[]
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
