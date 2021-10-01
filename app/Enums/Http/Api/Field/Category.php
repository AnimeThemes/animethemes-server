<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Field;

use App\Enums\BaseEnum;

/**
 * Class Category.
 *
 * @method static static ATTRIBUTE()
 * @method static static COMPUTED()
 */
final class Category extends BaseEnum
{
    public const ATTRIBUTE = 0;
    public const COMPUTED = 1;
}
