<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Unions;

use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\EloquentType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\UnionType;

abstract class BaseUnion extends UnionType
{
    /**
     * The attributes of the union.
     *
     * @return array<string,mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->description(),
        ];
    }

    /**
     * The name of the union type.
     * By default, it will be the class name.
     */
    public function getName(): string
    {
        return class_basename($this);
    }

    /**
     * The description of the union type.
     */
    public function description(): string
    {
        return '';
    }

    /**
     * The types converted to the base.
     *
     * @return Type[]
     */
    public function types(): array
    {
        return collect($this->baseTypes())
            ->map(fn (BaseType $type) => GraphQL::type($type->getName()))
            ->toArray();
    }

    public function resolveType($value): Type
    {
        $baseType = collect($this->baseTypes())
            ->filter(fn (BaseType $type) => $type instanceof EloquentType)
            ->first(fn (EloquentType $type) => $type->model() === $value::class);

        return GraphQL::type($baseType->getName());
    }

    /**
     * The types that this union can resolve to.
     *
     * @return BaseType[]
     */
    abstract public function baseTypes(): array;
}
