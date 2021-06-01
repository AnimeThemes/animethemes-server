<?php declare(strict_types=1);

namespace App\Enums\JsonApi;

use BenSampo\Enum\Enum;

/**
 * @method static static NONE()
 * @method static static LIMIT()
 * @method static static OFFSET()
 */
final class PaginationStrategy extends Enum
{
    public const NONE = 0;
    public const LIMIT = 1;
    public const OFFSET = 2;
}
