<?php

declare(strict_types=1);

namespace App\Enums\Models\User;

enum EncodeType: int
{
    case OLD = 0;
    case CURRENT = 1;
}
