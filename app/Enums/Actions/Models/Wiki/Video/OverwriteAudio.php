<?php

declare(strict_types=1);

namespace App\Enums\Actions\Models\Wiki\Video;

use App\Enums\BaseEnum;

/**
 * Class OverwriteAudio.
 *
 * @method static static YES()
 * @method static static NO()
 */
final class OverwriteAudio extends BaseEnum
{
    public const YES = 0;
    public const NO = 1;
}
