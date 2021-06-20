<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Class ImageFacet.
 */
final class ImageFacet extends Enum implements LocalizedEnum
{
    public const COVER_SMALL = 0;
    public const COVER_LARGE = 1;
}
