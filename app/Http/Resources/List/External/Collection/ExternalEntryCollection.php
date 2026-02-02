<?php

declare(strict_types=1);

namespace App\Http\Resources\List\External\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\List\External\Resource\ExternalEntryJsonResource;
use App\Models\List\External\ExternalEntry;
use Illuminate\Http\Request;

class ExternalEntryCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'externalentries';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (ExternalEntry $entry): ExternalEntryJsonResource => new ExternalEntryJsonResource($entry, $this->query))->all();
    }
}
