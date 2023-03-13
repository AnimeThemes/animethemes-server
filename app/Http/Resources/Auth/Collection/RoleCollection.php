<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth\Collection;

use App\Http\Resources\Auth\Resource\RoleResource;
use App\Http\Resources\BaseCollection;
use App\Models\Auth\Role;
use Illuminate\Http\Request;

/**
 * Class RoleCollection.
 */
class RoleCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'roles';

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
        return $this->collection->map(fn (Role $role) => new RoleResource($role, $this->query))->all();
    }
}
