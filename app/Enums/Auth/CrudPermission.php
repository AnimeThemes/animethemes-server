<?php

declare(strict_types=1);

namespace App\Enums\Auth;

use App\Concerns\Enums\FormatsPermission;

/**
 * Enum CrudPermissions.
 */
enum CrudPermission: string
{
    use FormatsPermission;

    case CREATE = 'create';

    case DELETE = 'delete';

    case UPDATE = 'update';

    case VIEW = 'view';
}
