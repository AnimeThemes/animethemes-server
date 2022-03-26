<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_BASENAME)) {
            $result[Video::ATTRIBUTE_BASENAME] = $this->basename;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_FILENAME)) {
            $result[Video::ATTRIBUTE_FILENAME] = $this->filename;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_PATH)) {
            $result[Video::ATTRIBUTE_PATH] = $this->path;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_SIZE)) {
            $result[Video::ATTRIBUTE_SIZE] = $this->size;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_MIMETYPE)) {
            $result[Video::ATTRIBUTE_MIMETYPE] = $this->mimetype;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_RESOLUTION)) {
            $result[Video::ATTRIBUTE_RESOLUTION] = $this->resolution;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_NC)) {
            $result[Video::ATTRIBUTE_NC] = $this->nc;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_SUBBED)) {
            $result[Video::ATTRIBUTE_SUBBED] = $this->subbed;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_LYRICS)) {
            $result[Video::ATTRIBUTE_LYRICS] = $this->lyrics;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_UNCEN)) {
            $result[Video::ATTRIBUTE_UNCEN] = $this->uncen;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_SOURCE)) {
            $result[Video::ATTRIBUTE_SOURCE] = $this->source?->description;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_OVERLAP)) {
            $result[Video::ATTRIBUTE_OVERLAP] = $this->overlap->description;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        if ($this->isAllowedField(Video::ATTRIBUTE_TAGS)) {
            $result[Video::ATTRIBUTE_TAGS] = implode('', $this->tags);
        }

        if ($this->isAllowedField(VideoResource::ATTRIBUTE_LINK)) {
            $result[VideoResource::ATTRIBUTE_LINK] = route('video.show', $this);
        }

        $result[Video::RELATION_ANIMETHEMEENTRIES] = EntryCollection::make($this->whenLoaded(Video::RELATION_ANIMETHEMEENTRIES), $this->query);

        return $result;
    }
}
