<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Collection;

use App\Http\Resources\Admin\Resource\FeaturedThemeResource;
use App\Http\Resources\BaseCollection;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Http\Request;

/**
 * Class FeaturedThemeCollection.
 */
class FeaturedThemeCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'featuredthemes';

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(
            fn (FeaturedTheme $featuredTheme) => new FeaturedThemeResource($featuredTheme, $this->query)
        )->all();
    }
}
