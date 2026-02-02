<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Theme\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryJsonResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\Request;

class EntryCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animethemeentries';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (AnimeThemeEntry $entry): EntryJsonResource => new EntryJsonResource($entry, $this->query))->all();
    }
}
