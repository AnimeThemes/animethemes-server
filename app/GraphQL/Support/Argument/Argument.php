<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Argument;

use GraphQL\Type\Definition\Type;

class Argument
{
    protected bool $required = false;
    protected mixed $defaultValue = null;

    /**
     * @var array<string, array>
     */
    protected array $directives = [];

    public function __construct(
        public string $name,
        public Type|string $returnType,
    ) {}

    /**
     * Mark the argument as required.
     */
    public function required(bool $condition = true): static
    {
        $this->required = $condition;

        return $this;
    }

    /**
     * Append a default value to the argument.
     */
    public function withDefaultValue(mixed $value): static
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * Get the default value.
     */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * Get the type of the argument.
     */
    public function getType(): Type
    {
        return $this->required
            ? Type::nonNull($this->returnType)
            : $this->returnType;
    }
}
