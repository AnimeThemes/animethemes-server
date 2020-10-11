<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class UserType extends Enum implements LocalizedEnum
{
    const READ_ONLY = 0;
    const CONTRIBUTOR = 1;
    const ADMIN = 2;
}
