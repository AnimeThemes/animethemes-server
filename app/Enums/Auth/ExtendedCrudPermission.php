<?php

declare(strict_types=1);

namespace App\Enums\Auth;

/**
 * Class ExtendedCrudPermission.
 *
 * @method static static FORCE_DELETE()
 * @method static static RESTORE()
 */
final class ExtendedCrudPermission extends CrudPermission
{
    public const FORCE_DELETE = 'force delete';

    public const RESTORE = 'restore';
}
