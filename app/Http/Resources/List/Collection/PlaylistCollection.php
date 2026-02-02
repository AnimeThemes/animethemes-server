<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\List\Resource\PlaylistJsonResource;
use App\Models\List\Playlist;
use Illuminate\Http\Request;

class PlaylistCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'playlists';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (Playlist $playlist): PlaylistJsonResource => new PlaylistJsonResource($playlist, $this->query))->all();
    }
}
