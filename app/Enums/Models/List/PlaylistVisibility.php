<?php

declare(strict_types=1);

namespace App\Enums\Models\List;

use App\Enums\BaseEnum;

/**
 * Class PlaylistVisibility.
 *
 * @method static static PUBLIC()
 * @method static static PRIVATE()
 * @method static static UNLISTED()
 */
final class PlaylistVisibility extends BaseEnum
{
    public const PUBLIC = 0;
    public const PRIVATE = 1;
    public const UNLISTED = 2;
}
