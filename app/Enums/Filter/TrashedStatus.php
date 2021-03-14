<?php

namespace App\Enums\Filter;

use BenSampo\Enum\Enum;

final class TrashedStatus extends Enum
{
    const WITH = 'with';
    const WITHOUT = 'without';
    const ONLY = 'only';
}
