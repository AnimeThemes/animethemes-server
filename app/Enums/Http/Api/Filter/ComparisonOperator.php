<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

use BenSampo\Enum\Enum;

/**
 * Class ComparisonOperator.
 *
 * @method static static EQ()
 * @method static static NE()
 * @method static static LT()
 * @method static static GT()
 * @method static static LTE()
 * @method static static GTE()
 * @method static static LIKE()
 * @method static static NOTLIKE()
 */
final class ComparisonOperator extends Enum
{
    public const EQ = '=';
    public const NE = '<>';
    public const LT = '<';
    public const GT = '>';
    public const LTE = '<=';
    public const GTE = '>=';
    public const LIKE = 'like';
    public const NOTLIKE = 'not like';
}
