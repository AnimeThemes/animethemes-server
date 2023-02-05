<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Http\Resources\Wiki\Video\Resource\ScriptResource;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class VideoResource.
 */
class VideoResource extends BaseResource
{
    final public const ATTRIBUTE_LINK = 'link';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'video';

    /**
     * Create a new resource instance.
     *
     * @param  Video | MissingValue | null  $video
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Video|MissingValue|null $video, ReadQuery $query)
    {
        parent::__construct($video, $query);
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

        $result[Video::RELATION_ANIMETHEMEENTRIES] = new EntryCollection($this->whenLoaded(Video::RELATION_ANIMETHEMEENTRIES), $this->query);
        $result[Video::RELATION_AUDIO] = new AudioResource($this->whenLoaded(Video::RELATION_AUDIO), $this->query);
        $result[Video::RELATION_SCRIPT] = new ScriptResource($this->whenLoaded(Video::RELATION_SCRIPT), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new VideoSchema();
    }
}
