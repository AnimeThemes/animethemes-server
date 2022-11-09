<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Playlist\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\BaseModel;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class TrackResource.
 *
 * @mixin PlaylistTrack
 */
class TrackResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'track';

    /**
     * Create a new resource instance.
     *
     * @param  PlaylistTrack | MissingValue | null  $track
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(PlaylistTrack|MissingValue|null $track, ReadQuery $query)
    {
        parent::__construct($track, $query);
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

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        $result[PlaylistTrack::RELATION_PLAYLIST] = new PlaylistResource($this->whenLoaded(PlaylistTrack::RELATION_PLAYLIST), $this->query);
        $result[PlaylistTrack::RELATION_NEXT] = new TrackResource($this->whenLoaded(PlaylistTrack::RELATION_NEXT), $this->query);
        $result[PlaylistTrack::RELATION_PREVIOUS] = new TrackResource($this->whenLoaded(PlaylistTrack::RELATION_PREVIOUS), $this->query);
        $result[PlaylistTrack::RELATION_VIDEO] = new VideoResource($this->whenLoaded(PlaylistTrack::RELATION_VIDEO), $this->query);

        return $result;
    }
}
