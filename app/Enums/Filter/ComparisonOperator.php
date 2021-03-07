<?php

namespace App\Enums\Filter;

use BenSampo\Enum\Enum;

final class ComparisonOperator extends Enum
{
    const EQ = '=';
    const NE = '<>';
    const LT = '<';
    const GT = '>';
    const LTE = '<=';
    const GTE = '>=';
}
