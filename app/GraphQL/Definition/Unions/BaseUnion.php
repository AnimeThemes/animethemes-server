<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Unions;

use App\GraphQL\Definition\Types\BaseType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class BaseUnion extends UnionType
{
    /**
     * Initialize the union type with its name, description, and types.
     */
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
     *
     * @return string
     */
    public function toGraphQLString(): string
    {
        return Str::of('union ')
            ->append($this->name())
            ->append(' = ')
            ->append(implode(' | ', Arr::map($this->types(), fn (BaseType $type) => $type->name())))
            ->newLine()
            ->__toString();
    }

    /**
     * The name of the union type.
     *
     * @return string
     */
    public function name(): string
    {
        return Str::of(class_basename($this))
            ->__toString();
    }

    /**
     * The description of the union type.
     *
     * @return string
     */
    public function description(): string
    {
        return '';
    }

    /**
     * The types that this union can resolve to.
     *
     * @return Type[]
     */
    abstract public function types(): array;
}
