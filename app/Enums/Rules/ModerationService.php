<?php

declare(strict_types=1);

namespace App\Enums\Rules;

enum ModerationService: string
{
    case NONE = 'none';
    case OPENAI = 'openai';
}
