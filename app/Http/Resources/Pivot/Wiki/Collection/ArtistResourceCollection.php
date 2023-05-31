<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistResourceResource;
use App\Pivots\Wiki\ArtistResource;
use Illuminate\Http\Request;

/**
 * Class ArtistResourceCollection.
 */
class ArtistResourceCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistresources';

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
        return $this->collection->map(fn (ArtistResource $artistResource) => new ArtistResourceResource($artistResource, $this->query))->all();
    }
}
