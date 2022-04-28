<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Enums\BaseEnum;

/**
 * Class ImageFacet.
 *
 * @method static static COVER_SMALL()
 * @method static static COVER_LARGE()
e * @method static static GRILL()
 */
final class ImageFacet extends BaseEnum
{
    public const COVER_SMALL = 0;
    public const COVER_LARGE = 1;
    public const GRILL = 2;
}
