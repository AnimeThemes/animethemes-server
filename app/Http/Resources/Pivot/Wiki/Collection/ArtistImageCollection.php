<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistImageResource;
use App\Pivots\Wiki\ArtistImage;
use Illuminate\Http\Request;

/**
 * Class ArtistImageCollection.
 */
class ArtistImageCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistimages';

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
        return $this->collection->map(fn (ArtistImage $artistImage) => new ArtistImageResource($artistImage, $this->query))->all();
    }
}
