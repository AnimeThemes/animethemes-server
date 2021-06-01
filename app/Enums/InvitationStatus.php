<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Class InvitationStatus.
 */
final class InvitationStatus extends Enum implements LocalizedEnum
{
    public const OPEN = 0;
    public const CLOSED = 1;
}
