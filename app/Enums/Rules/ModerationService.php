<?php

declare(strict_types=1);

namespace App\Enums\Rules;

use App\Enums\BaseEnum;

/**
 * Class ModerationService.
 *
 * @method static static NONE()
 * @method static static OPENAI()
 */
final class ModerationService extends BaseEnum
{
    public const NONE = 'none';
    public const OPENAI = 'openai';
}
