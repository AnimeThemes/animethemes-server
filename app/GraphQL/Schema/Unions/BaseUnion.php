<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Unions;

use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\EloquentType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\UnionType;

abstract class BaseUnion extends UnionType
{
    /**
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
            ->all();
    }

    public function resolveType($value): Type
    {
        $baseType = collect($this->baseTypes())
            ->filter(fn (BaseType $type): bool => $type instanceof EloquentType)
            ->first(fn (EloquentType $type): bool => $type->model() === $value::class);

        return GraphQL::type($baseType->getName());
    }

    /**
     * The types that this union can resolve to.
     *
     * @return BaseType[]
     */
    abstract public function baseTypes(): array;
}
