<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\Auth\Resource\UserResource;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class PlaylistResource.
 *
 * @mixin Playlist
 */
class PlaylistResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'playlist';

    /**
     * Create a new resource instance.
     *
     * @param  Playlist | MissingValue | null  $playlist
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Playlist|MissingValue|null $playlist, ReadQuery $query)
    {
        parent::__construct($playlist, $query);
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

        if ($this->isAllowedField(Playlist::ATTRIBUTE_NAME)) {
            $result[Playlist::ATTRIBUTE_NAME] = $this->name;
        }

        if ($this->isAllowedField(Playlist::ATTRIBUTE_VISIBILITY)) {
            $result[Playlist::ATTRIBUTE_VISIBILITY] = $this->visibility->description;
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

        $result[Playlist::RELATION_USER] = new UserResource($this->whenLoaded(Playlist::RELATION_USER), $this->query);
        $result[Playlist::RELATION_FIRST] = new TrackResource($this->whenLoaded(Playlist::RELATION_FIRST), $this->query);
        $result[Playlist::RELATION_LAST] = new TrackResource($this->whenLoaded(Playlist::RELATION_LAST), $this->query);
        $result[Playlist::RELATION_IMAGES] = new ImageCollection($this->whenLoaded(Playlist::RELATION_IMAGES), $this->query);
        $result[Playlist::RELATION_TRACKS] = new TrackCollection($this->whenLoaded(Playlist::RELATION_TRACKS), $this->query);

        return $result;
    }
}
