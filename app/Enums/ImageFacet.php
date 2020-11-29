<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class ImageFacet extends Enum implements LocalizedEnum
{
    const COVER_SMALL = 0;
    const COVER_LARGE = 1;
}
