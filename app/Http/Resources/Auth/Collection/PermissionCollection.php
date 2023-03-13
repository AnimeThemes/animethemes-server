<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth\Collection;

use App\Http\Resources\Auth\Resource\PermissionResource;
use App\Http\Resources\BaseCollection;
use App\Models\Auth\Permission;
use Illuminate\Http\Request;

/**
 * Class PermissionCollection.
 */
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
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(fn (Permission $permission) => new PermissionResource($permission, $this->query))->all();
    }
}
