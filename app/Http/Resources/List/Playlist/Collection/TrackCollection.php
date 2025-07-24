<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Playlist\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Http\Request;

class TrackCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'tracks';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (PlaylistTrack $track) => new TrackResource($track, $this->query))->all();
    }
}
