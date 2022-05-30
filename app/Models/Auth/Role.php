<?php

declare(strict_types=1);

namespace App\Models\Auth;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use Spatie\Permission\Models\Role as BaseRole;

/**
 * Class Role.
 *
 * @property Carbon $created_at
 * @property Carbon $deleted_at
 * @property string $guard_name
 * @property int $id
 * @property string $name
 */
class Role extends BaseRole
{
    use Actionable;

    final public const TABLE = 'roles';

    final public const ATTRIBUTE_CREATED_AT = Model::CREATED_AT;
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_UPDATED_AT = Model::UPDATED_AT;

    final public const RELATION_PERMISSIONS = 'permissions';
    final public const RELATION_USERS = 'users';
}
