<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Argument;

use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\SortableColumns;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SortArgument extends Argument
{
    final public const ARGUMENT = 'sort';

    public function __construct(protected BaseType $type)
    {
        $sortableColumns = new SortableColumns($type);

        $name = Arr::get($sortableColumns->getAttributes(), 'name');

        parent::__construct(self::ARGUMENT, Type::listOf(Type::nonNull(GraphQL::type($name))));
    }
}
