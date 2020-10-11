<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class InvitationStatus extends Enum implements LocalizedEnum
{
    const OPEN = 0;
    const CLOSED = 1;
}
