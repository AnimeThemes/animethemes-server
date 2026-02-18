<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\SynonymJsonResource;
use App\Models\Wiki\Synonym;
use Illuminate\Http\Request;

class SynonymCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'synonyms';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(
            fn (Synonym $synonym): SynonymJsonResource => new SynonymJsonResource($synonym, $this->query)
        )->all();
    }
}
