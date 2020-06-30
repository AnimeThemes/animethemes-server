<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class InvitationStatus extends Enum implements LocalizedEnum
{
    const OPEN    = 0;
    const CLOSED  = 1;
}
