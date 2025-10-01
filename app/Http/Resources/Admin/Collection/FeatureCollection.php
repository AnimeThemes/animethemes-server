<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Collection;

use App\Http\Resources\Admin\Resource\FeatureResource;
use App\Http\Resources\BaseCollection;
use App\Models\Admin\Feature;
use Illuminate\Http\Request;

class FeatureCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'features';

    /**
     * Transform the resource collection into an array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(
            fn (Feature $feature): FeatureResource => new FeatureResource($feature, $this->query)
        )->all();
    }
}
