<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\ArtistSongSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Http\Request;

/**
 * Class ArtistSongResource.
 */
class ArtistSongResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistsong';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[ArtistSong::RELATION_ARTIST] = new ArtistResource($this->whenLoaded(ArtistSong::RELATION_ARTIST), $this->query);
        $result[ArtistSong::RELATION_SONG] = new SongResource($this->whenLoaded(ArtistSong::RELATION_SONG), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ArtistSongSchema();
    }
}
