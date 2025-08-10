<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Unions;

use App\GraphQL\Definition\Types\BaseType;
use GraphQL\Type\Definition\UnionType;
use Illuminate\Support\Str;

abstract class BaseUnion extends UnionType
{
    public function __construct()
    {
        parent::__construct([
            'name' => $this->name(),
            'description' => $this->description(),
            'types' => $this->types(),
        ]);
    }

    /**
     * Mount the type definition string.
     */
    public function toGraphQLString(): string
    {
        return Str::of('union ')
            ->append($this->name())
            ->append(' = ')
            ->append(collect($this->types())->map(fn (BaseType $type) => $type->getName())->implode(' | '))
            ->newLine()
            ->__toString();
    }

    /**
     * The name of the union type.
     * By default, it will be the class name.
     */
    public function name(): string
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
     * The types that this union can resolve to.
     *
     * @return BaseType[]
     */
    abstract public function types(): array;
}
