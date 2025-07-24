<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\GroupResource;
use App\Models\Wiki\Group;
use Illuminate\Http\Request;

class GroupCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'groups';

    /**
     * Transform the resource into a JSON array.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (Group $group) => new GroupResource($group, $this->query))->all();
    }
}
