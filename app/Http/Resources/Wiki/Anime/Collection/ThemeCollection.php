<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Anime\Resource\ThemeJsonResource;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;

class ThemeCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animethemes';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (AnimeTheme $theme): ThemeJsonResource => new ThemeJsonResource($theme, $this->query))->all();
    }
}
