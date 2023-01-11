<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

use App\Enums\BaseEnum;

/**
 * Class Clause.
 *
 * @method static static WHERE()
 * @method static static HAVING()
 */
final class Clause extends BaseEnum
{
    public const WHERE = 0;
    public const HAVING = 1;
}
