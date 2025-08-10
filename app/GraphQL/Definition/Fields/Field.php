<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Concerns\GraphQL\ResolvesArguments;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Field
{
    use ResolvesArguments;

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

    /**
     * Get the column of the field.
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return '';
    }

    /**
     * The type returned by the field.
     */
    public function getType(): Type
    {
        if (! $this->nullable) {
            return Type::nonNull($this->type());
        }

        return $this->type();
    }

    /**
     * The type returned by the field.
     */
    abstract public function type(): Type;

    /**
     * Resolve the field.
     */
    public function resolve($root): mixed
    {
        return Arr::get($root, $this->column);
    }
}
