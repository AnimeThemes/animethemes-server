<?php

declare(strict_types=1);

namespace App\GraphQL\Argument;

use App\Contracts\GraphQL\EnumSort;
use App\GraphQL\Schema\Types\BaseType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use UnitEnum;

class SortArgument extends Argument
{
    final public const string ARGUMENT = 'sort';

    /**
     * @param  class-string<UnitEnum&EnumSort>  $sortEnum
     */
    public function __construct(protected BaseType $type, ?string $sortEnum = null)
    {
        $enum = $sortEnum ?? $type->getEnumSortClass();

        parent::__construct(self::ARGUMENT, Type::listOf(Type::nonNull(GraphQL::type(class_basename($enum)))));
    }
}
