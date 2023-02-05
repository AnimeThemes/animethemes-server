<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Auth\Resource\UserResource;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Models\List\Playlist;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class PlaylistResource.
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
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[Playlist::RELATION_USER] = new UserResource($this->whenLoaded(Playlist::RELATION_USER), $this->query);
        $result[Playlist::RELATION_FIRST] = new TrackResource($this->whenLoaded(Playlist::RELATION_FIRST), $this->query);
        $result[Playlist::RELATION_LAST] = new TrackResource($this->whenLoaded(Playlist::RELATION_LAST), $this->query);
        $result[Playlist::RELATION_IMAGES] = new ImageCollection($this->whenLoaded(Playlist::RELATION_IMAGES), $this->query);
        $result[Playlist::RELATION_TRACKS] = new TrackCollection($this->whenLoaded(Playlist::RELATION_TRACKS), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new PlaylistSchema();
    }
}
