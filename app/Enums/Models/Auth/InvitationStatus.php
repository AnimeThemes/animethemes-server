<?php

declare(strict_types=1);

namespace App\Enums\Models\Auth;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Class InvitationStatus.
 *
 * @method static static OPEN()
 * @method static static CLOSED()
 */
final class InvitationStatus extends Enum implements LocalizedEnum
{
    public const OPEN = 0;
    public const CLOSED = 1;
}
