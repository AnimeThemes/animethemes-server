<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Sort;

use BenSampo\Enum\Enum;

/**
 * Class Direction.
 *
 * @method static static ASCENDING()
 * @method static static DESCENDING()
 */
final class Direction extends Enum
{
    public const ASCENDING = 'asc';
    public const DESCENDING = 'desc';
}
