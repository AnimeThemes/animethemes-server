<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Series;
use Illuminate\Http\Request;

/**
 * Class SeriesCollection.
 */
class SeriesCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'series';

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
        return $this->collection->map(fn (Series $series) => new SeriesResource($series, $this->query))->all();
    }
}
