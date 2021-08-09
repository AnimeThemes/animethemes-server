<?php

declare(strict_types=1);

namespace App\Enums\Models\Auth;

use App\Enums\BaseEnum;

/**
 * Class InvitationStatus.
 *
 * @method static static OPEN()
 * @method static static CLOSED()
 */
final class InvitationStatus extends BaseEnum
{
    public const OPEN = 0;
    public const CLOSED = 1;
}
