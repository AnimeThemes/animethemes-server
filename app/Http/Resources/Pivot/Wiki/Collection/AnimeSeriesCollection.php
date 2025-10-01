<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesResource;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Http\Request;

class AnimeSeriesCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animeseries';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (AnimeSeries $animeSeries): AnimeSeriesResource => new AnimeSeriesResource($animeSeries, $this->query))->all();
    }
}
