<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Field;

use App\Enums\BaseEnum;

/**
 * Class AggregateFunction.
 *
 * @method static static AVG()
 * @method static static COUNT()
 * @method static static EXISTS()
 * @method static static MAX()
 * @method static static MIN()
 * @method static static SUM()
 */
final class AggregateFunction extends BaseEnum
{
    public const AVG = 'avg';
    public const COUNT = 'count';
    public const EXISTS = 'exists';
    public const MAX = 'max';
    public const MIN = 'min';
    public const SUM = 'sum';
}
