<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\SongResourceResource;
use App\Pivots\Wiki\SongResource;
use Illuminate\Http\Request;

class SongResourceCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'songresources';

    /**
     * Transform the resource into a JSON array.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (SongResource $animeResource) => new SongResourceResource($animeResource, $this->query))->all();
    }
}
