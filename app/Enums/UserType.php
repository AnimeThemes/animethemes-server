<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class UserType extends Enum implements LocalizedEnum
{
    const READ_ONLY   = 0;
    const CONTRIBUTOR = 1;
    const ADMIN       = 2;
}
