<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth\Collection;

use App\Http\Resources\Auth\Resource\PermissionJsonResource;
use App\Http\Resources\BaseCollection;
use App\Models\Auth\Permission;
use Illuminate\Http\Request;

class PermissionCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'permissions';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (Permission $permission): PermissionJsonResource => new PermissionJsonResource($permission, $this->query))->all();
    }
}
