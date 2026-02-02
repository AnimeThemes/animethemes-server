<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioJsonResource;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Http\Request;

class AnimeStudioCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animestudios';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (AnimeStudio $animeStudio): AnimeStudioJsonResource => new AnimeStudioJsonResource($animeStudio, $this->query))->all();
    }
}
