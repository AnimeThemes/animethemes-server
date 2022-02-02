<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class VideoResource.
 *
 * @mixin Video
 */
class VideoResource extends BaseResource
{
    public const ATTRIBUTE_LINK = 'link';

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
     * @param  Query  $query
     * @return void
     */
    public function __construct(Video|MissingValue|null $video, Query $query)
    {
        parent::__construct($video, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            BaseResource::ATTRIBUTE_ID => $this->when($this->isAllowedField(BaseResource::ATTRIBUTE_ID), $this->getKey()),
            Video::ATTRIBUTE_BASENAME => $this->when($this->isAllowedField(Video::ATTRIBUTE_BASENAME), $this->basename),
            Video::ATTRIBUTE_FILENAME => $this->when($this->isAllowedField(Video::ATTRIBUTE_FILENAME), $this->filename),
            Video::ATTRIBUTE_PATH => $this->when($this->isAllowedField(Video::ATTRIBUTE_PATH), $this->path),
            Video::ATTRIBUTE_SIZE => $this->when($this->isAllowedField(Video::ATTRIBUTE_SIZE), $this->size),
            Video::ATTRIBUTE_MIMETYPE => $this->when($this->isAllowedField(Video::ATTRIBUTE_MIMETYPE), $this->mimetype),
            Video::ATTRIBUTE_RESOLUTION => $this->when($this->isAllowedField(Video::ATTRIBUTE_RESOLUTION), $this->resolution),
            Video::ATTRIBUTE_NC => $this->when($this->isAllowedField(Video::ATTRIBUTE_NC), $this->nc),
            Video::ATTRIBUTE_SUBBED => $this->when($this->isAllowedField(Video::ATTRIBUTE_SUBBED), $this->subbed),
            Video::ATTRIBUTE_LYRICS => $this->when($this->isAllowedField(Video::ATTRIBUTE_LYRICS), $this->lyrics),
            Video::ATTRIBUTE_UNCEN => $this->when($this->isAllowedField(Video::ATTRIBUTE_UNCEN), $this->uncen),
            Video::ATTRIBUTE_SOURCE => $this->when($this->isAllowedField(Video::ATTRIBUTE_SOURCE), $this->source?->description),
            Video::ATTRIBUTE_OVERLAP => $this->when($this->isAllowedField(Video::ATTRIBUTE_OVERLAP), $this->overlap->description),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
            Video::ATTRIBUTE_TAGS => $this->when($this->isAllowedField(Video::ATTRIBUTE_TAGS), implode('', $this->tags)),
            VideoResource::ATTRIBUTE_LINK => $this->when($this->isAllowedField(VideoResource::ATTRIBUTE_LINK), route('video.show', $this)),
            Video::RELATION_ANIMETHEMEENTRIES => EntryCollection::make($this->whenLoaded(Video::RELATION_ANIMETHEMEENTRIES), $this->query),
        ];
    }
}
