<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Pivot\Wiki\AnimeThemeEntryVideoSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AnimeThemeEntryVideoResource.
 */
class AnimeThemeEntryVideoResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animethemeentryvideo';

    /**
     * Create a new resource instance.
     *
     * @param  AnimeThemeEntryVideo | MissingValue | null  $pivot
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(AnimeThemeEntryVideo|MissingValue|null $pivot, ReadQuery $query)
    {
        parent::__construct($pivot, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[AnimeThemeEntryVideo::RELATION_ENTRY] = new EntryResource($this->whenLoaded(AnimeThemeEntryVideo::RELATION_ENTRY), $this->query);
        $result[AnimeThemeEntryVideo::RELATION_VIDEO] = new VideoResource($this->whenLoaded(AnimeThemeEntryVideo::RELATION_VIDEO), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new AnimeThemeEntryVideoSchema();
    }
}
