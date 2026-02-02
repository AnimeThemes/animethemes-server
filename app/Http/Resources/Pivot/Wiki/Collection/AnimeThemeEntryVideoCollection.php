<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoJsonResource;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Http\Request;

class AnimeThemeEntryVideoCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animethemeentryvideos';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (AnimeThemeEntryVideo $entryVideo): AnimeThemeEntryVideoJsonResource => new AnimeThemeEntryVideoJsonResource($entryVideo, $this->query))->all();
    }
}
