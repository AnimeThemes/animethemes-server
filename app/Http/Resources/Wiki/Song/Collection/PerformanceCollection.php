<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Song\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Song\Resource\PerformanceJsonResource;
use App\Models\Wiki\Song\Performance;
use Illuminate\Http\Request;

class PerformanceCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'performances';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (Performance $performance): PerformanceJsonResource => new PerformanceJsonResource($performance, $this->query))->all();
    }
}
