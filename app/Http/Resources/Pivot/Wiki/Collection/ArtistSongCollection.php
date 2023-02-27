<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Http\Request;

/**
 * Class ArtistSongCollection.
 */
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
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(fn (ArtistSong $artistSong) => new ArtistSongResource($artistSong, $this->query))->all();
    }
}
