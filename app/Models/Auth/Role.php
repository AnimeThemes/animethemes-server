<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Contracts\Models\Nameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as BaseRole;

/**
 * Class Role.
 *
 * @property string $color
 * @property bool $default
 * @property Carbon $created_at
 * @property string $guard_name
 * @property int $id
 * @property string $name
 * @property int $priority
 * @property Carbon $updated_at
 */
class Role extends BaseRole implements Nameable
{
    final public const TABLE = 'roles';

    final public const ATTRIBUTE_COLOR = 'color';
    final public const ATTRIBUTE_CREATED_AT = Model::CREATED_AT;
    final public const ATTRIBUTE_DEFAULT = 'default';
    final public const ATTRIBUTE_GUARD_NAME = 'guard_name';
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_PRIORITY = 'priority';
    final public const ATTRIBUTE_UPDATED_AT = Model::UPDATED_AT;

    final public const RELATION_PERMISSIONS = 'permissions';
    final public const RELATION_USERS = 'users';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Role::ATTRIBUTE_DEFAULT => 'boolean',
            Role::ATTRIBUTE_PRIORITY => 'int',
        ];
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
