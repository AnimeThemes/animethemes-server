<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

/**
 * Class BinaryLogicalOperator.
 *
 * @method static static AND()
 * @method static static OR()
 */
final class BinaryLogicalOperator extends LogicalOperator
{
    public const AND = 'and';
    public const OR = 'or';
}
