<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Http\Request;

class SynonymCollection extends BaseCollection
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
            fn (AnimeSynonym $synonym): SynonymResource => new SynonymResource($synonym, $this->query)
        )->all();
    }
}
