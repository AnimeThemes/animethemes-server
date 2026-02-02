<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongJsonResource;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Http\Request;

class ArtistSongCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistsongs';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (ArtistSong $artistSong): ArtistSongJsonResource => new ArtistSongJsonResource($artistSong, $this->query))->all();
    }
}
