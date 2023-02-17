<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Playlist\Resource;

use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Http\Request;

/**
 * Class TrackResource.
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
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[PlaylistTrack::RELATION_PLAYLIST] = new PlaylistResource($this->whenLoaded(PlaylistTrack::RELATION_PLAYLIST), $this->query);
        $result[PlaylistTrack::RELATION_NEXT] = new TrackResource($this->whenLoaded(PlaylistTrack::RELATION_NEXT), $this->query);
        $result[PlaylistTrack::RELATION_PREVIOUS] = new TrackResource($this->whenLoaded(PlaylistTrack::RELATION_PREVIOUS), $this->query);
        $result[PlaylistTrack::RELATION_VIDEO] = new VideoResource($this->whenLoaded(PlaylistTrack::RELATION_VIDEO), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new TrackSchema();
    }
}
