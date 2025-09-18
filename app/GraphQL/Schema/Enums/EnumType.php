<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Enums;

use BackedEnum;
use GraphQL\Type\Definition\PhpEnumType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Contracts\TypeConvertible;
use UnitEnum;

class EnumType extends PhpEnumType implements TypeConvertible
{
    public function toType(): Type
    {
        return $this;
    }

    /**
     * @param  mixed  $value
     */
    public function serialize($value): string
    {
        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        if ($value instanceof UnitEnum) {
            return (string) $value->name;
        }

        return (string) $value;
    }
}
