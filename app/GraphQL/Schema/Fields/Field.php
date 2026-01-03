<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\GraphQL\Schema\Types\BaseType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class Field
{
    public function __construct(
        protected string $column,
        protected ?string $name = null,
        protected bool $nullable = true,
    ) {}

    /**
     * Get the name of the field.
     * By default, the name will be the column in camelCase.
     */
    public function getName(): string
    {
        return $this->name ?? Str::camel($this->column);
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function description(): string
    {
        return '';
    }

    public function type(): Type
    {
        $baseType = $this->baseType();

        $type = $baseType instanceof BaseType
            ? GraphQL::type($baseType->getName())
            : $baseType;

        if (! $this->nullable) {
            return Type::nonNull($type);
        }

        return $type;
    }

    abstract public function baseType(): Type|BaseType;

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return Arr::get($root, $this->column);
    }
}
