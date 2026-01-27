<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Inputs;

use App\Enums\GraphQL\Filter\ComparisonOperator;
use App\GraphQL\Schema\Enums\FilterableColumns;
use App\GraphQL\Schema\Types\EloquentType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;

class WhereConditionsInput extends Input
{
    public function __construct(protected EloquentType $type)
    {
        $this->attributes['name'] = $this->getName();

        GraphQL::addType($this);

        GraphQL::addType(new FilterableColumns($type));
    }

    public function getName(): string
    {
        return Str::of($this->type->getName())
            ->remove('Type')
            ->append('WhereConditionsInput')
            ->__toString();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function fields(): array
    {
        return [
            'field' => [
                'type' => GraphQL::type(new FilterableColumns($this->type)->getName()),
            ],
            'value' => [
                'type' => GraphQL::type('Mixed'),
            ],
            'operator' => [
                'type' => Type::nonNull(GraphQL::type(class_basename(ComparisonOperator::class))),
                'defaultValue' => ComparisonOperator::EQ->name,
            ],
            'AND' => [
                'type' => Type::listOf(Type::nonNull(GraphQL::type($this->getName()))),
            ],
            'OR' => [
                'type' => Type::listOf(Type::nonNull(GraphQL::type($this->getName()))),
            ],
        ];
    }
}
