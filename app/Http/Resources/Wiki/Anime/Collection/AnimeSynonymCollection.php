<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Anime\Resource\AnimeSynonymJsonResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Http\Request;

class AnimeSynonymCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animesynonyms';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(
            fn (AnimeSynonym $synonym): AnimeSynonymJsonResource => new AnimeSynonymJsonResource($synonym, $this->query)
        )->all();
    }
}
