<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\List\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\List\Resource\PlaylistImageResource;
use App\Pivots\List\PlaylistImage;
use Illuminate\Http\Request;

/**
 * Class PlaylistImageCollection.
 */
class PlaylistImageCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'playlistimages';

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
        return $this->collection->map(fn (PlaylistImage $playlistImage) => new PlaylistImageResource($playlistImage, $this->query))->all();
    }
}
