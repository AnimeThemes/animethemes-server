<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Enums;

use GraphQL\Type\Definition\PhpEnumType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Contracts\TypeConvertible;

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
        return (string) $value;
    }
}
