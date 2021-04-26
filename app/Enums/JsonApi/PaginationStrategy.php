<?php

namespace App\Enums\JsonApi;

use BenSampo\Enum\Enum;

/**
 * @method static static NONE()
 * @method static static LIMIT()
 * @method static static OFFSET()
 */
final class PaginationStrategy extends Enum
{
    const NONE = 0;
    const LIMIT = 1;
    const OFFSET = 2;
}
