<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;

/**
 * Class ThemeCollection.
 */
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
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(fn (AnimeTheme $theme) => ThemeResource::make($theme, $this->query))->all();
    }
}
