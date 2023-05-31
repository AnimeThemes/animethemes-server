<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth\Collection;

use App\Http\Resources\Auth\Resource\UserResource;
use App\Http\Resources\BaseCollection;
use App\Models\Auth\User;
use Illuminate\Http\Request;

/**
 * Class UserCollection.
 */
class UserCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'users';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (User $user) => new UserResource($user, $this->query))->all();
    }
}
