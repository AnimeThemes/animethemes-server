<?php

declare(strict_types=1);

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission as BasePermission;

/**
 * Class Permission.
 *
 * @property Carbon $created_at
 * @property string $guard_name
 * @property int $id
 * @property string $name
 * @property Carbon $updated_at
 */
class Permission extends BasePermission
{
    final public const TABLE = 'permissions';

    final public const ATTRIBUTE_CREATED_AT = Model::CREATED_AT;
    final public const ATTRIBUTE_GUARD_NAME = 'guard_name';
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_UPDATED_AT = Model::UPDATED_AT;

    final public const RELATION_ROLES = 'roles';
    final public const RELATION_USERS = 'users';
}
