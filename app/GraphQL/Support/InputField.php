<?php

declare(strict_types=1);

namespace App\GraphQL\Support;

use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Stringable;

final readonly class InputField implements Stringable
{
    public function __construct(
        protected string $name,
        protected Type|string $type,
    ) {}

    /**
     * Get the input field as a string representation.
     */
    public function __toString(): string
    {
        return Str::of($this->name)
            ->append(': ')
            ->append($this->type instanceof Type ? $this->type->__toString() : $this->type)
            ->__toString();
    }
}
