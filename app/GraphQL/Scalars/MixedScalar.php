<?php

declare(strict_types=1);

namespace App\GraphQL\Scalars;

use GraphQL\Type\Definition\Type;
use MLL\GraphQLScalars\MixedScalar as GraphQLScalarsMixedScalar;
use Rebing\GraphQL\Support\Contracts\TypeConvertible;

class MixedScalar extends GraphQLScalarsMixedScalar implements TypeConvertible
{
    public function toType(): Type
    {
        return new MixedScalar();
    }
}
