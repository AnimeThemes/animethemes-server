<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoResource;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Http\Request;

/**
 * Class AnimeThemeEntryVideoCollection.
 */
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
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (AnimeThemeEntryVideo $entryVideo) => new AnimeThemeEntryVideoResource($entryVideo, $this->query))->all();
    }
}
