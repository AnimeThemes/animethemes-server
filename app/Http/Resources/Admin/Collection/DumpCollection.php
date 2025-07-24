<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Collection;

use App\Http\Resources\Admin\Resource\DumpResource;
use App\Http\Resources\BaseCollection;
use App\Models\Admin\Dump;
use Illuminate\Http\Request;

class DumpCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'dumps';

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
            fn (Dump $dump) => new DumpResource($dump, $this->query)
        )->all();
    }
}
