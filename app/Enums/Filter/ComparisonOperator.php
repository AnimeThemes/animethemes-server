<?php

namespace App\Enums\Filter;

use BenSampo\Enum\Enum;

/**
 * @method static static EQ()
 * @method static static NE()
 * @method static static LT()
 * @method static static GT()
 * @method static static LTE()
 * @method static static GTE()
 */
final class ComparisonOperator extends Enum
{
    const EQ = '=';
    const NE = '<>';
    const LT = '<';
    const GT = '>';
    const LTE = '<=';
    const GTE = '>=';
}
