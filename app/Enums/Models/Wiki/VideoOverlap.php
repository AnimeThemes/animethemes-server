<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Enums\BaseEnum;

/**
 * Class VideoOverlap.
 *
 * @method static static NONE()
 * @method static static TRANS()
 * @method static static OVER()
 */
final class VideoOverlap extends BaseEnum
{
    public const NONE = 0;
    public const TRANS = 1;
    public const OVER = 2;
}
