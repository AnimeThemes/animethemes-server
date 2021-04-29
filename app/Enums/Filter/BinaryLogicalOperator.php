<?php

namespace App\Enums\Filter;

use BenSampo\Enum\Enum;

/**
 * @method static static AND()
 * @method static static OR()
 */
final class BinaryLogicalOperator extends Enum
{
    const AND = 'and';
    const OR = 'or';
}
