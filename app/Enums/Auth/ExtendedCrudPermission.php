<?php

declare(strict_types=1);

namespace App\Enums\Auth;

use App\Concerns\Enums\FormatsPermission;

/**
 * Enum ExtendedCrudPermission.
 */
enum ExtendedCrudPermission: string
{
    use FormatsPermission;

    case FORCE_DELETE = 'force delete';

    case RESTORE = 'restore';
}
