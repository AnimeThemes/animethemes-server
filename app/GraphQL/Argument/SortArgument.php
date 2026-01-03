<?php

declare(strict_types=1);

namespace App\GraphQL\Argument;

use App\GraphQL\Schema\Enums\SortableColumns;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SortArgument extends Argument
{
    final public const string ARGUMENT = 'sort';

    public function __construct(protected BaseType $type, ?PivotType $pivotType = null)
    {
        $sortableColumns = new SortableColumns($type, $pivotType);

        GraphQL::addType($sortableColumns);

        $name = Arr::get($sortableColumns->getAttributes(), 'name');

        parent::__construct(self::ARGUMENT, Type::listOf(Type::nonNull(GraphQL::type($name))));
    }
}
