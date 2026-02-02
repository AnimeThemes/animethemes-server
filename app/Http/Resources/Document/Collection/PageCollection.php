<?php

declare(strict_types=1);

namespace App\Http\Resources\Document\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Document\Resource\PageJsonResource;
use App\Models\Document\Page;
use Illuminate\Http\Request;

class PageCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'pages';

    /**
     * Transform the resource collection into an array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(
            fn (Page $page): PageJsonResource => new PageJsonResource($page, $this->query)
        )->all();
    }
}
