<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Paging;

use App\Enums\BaseEnum;

/**
 * Class PaginationStrategy.
 *
 * @method static static NONE()
 * @method static static LIMIT()
 * @method static static OFFSET()
 */
final class PaginationStrategy extends BaseEnum
{
    public const NONE = 0;
    public const LIMIT = 1;
    public const OFFSET = 2;
}
