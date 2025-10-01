<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;
use Illuminate\Http\Request;

class SongCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'songs';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (Song $song): SongResource => new SongResource($song, $this->query))->all();
    }
}
