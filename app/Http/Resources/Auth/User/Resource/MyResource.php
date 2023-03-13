<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth\User\Resource;

use App\Http\Api\Schema\Auth\User\MySchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Auth\Collection\PermissionCollection;
use App\Http\Resources\Auth\Collection\RoleCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Models\Auth\User;
use Illuminate\Http\Request;

/**
 * Class UserResource.
 */
class MyResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'user';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[User::RELATION_PERMISSIONS] = new PermissionCollection($this->whenLoaded(User::RELATION_PERMISSIONS), $this->query);
        $result[User::RELATION_PLAYLISTS] = new PlaylistCollection($this->whenLoaded(User::RELATION_PLAYLISTS), $this->query);
        $result[User::RELATION_ROLES] = new RoleCollection($this->whenLoaded(User::RELATION_ROLES), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new MySchema();
    }
}
