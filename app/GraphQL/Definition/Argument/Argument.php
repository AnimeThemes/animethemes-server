<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Argument;

use App\Concerns\GraphQL\ResolvesDirectives;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Stringable;

readonly class Argument implements Stringable
{
    use ResolvesDirectives;

    /**
     * @param  array<string, array>  $directives
     */
    public function __construct(
        public string $name,
        public Type|string $returnType,
        public bool $required,
        public array $directives = [],
    ) {}

    /**
     * Get the resolved directives as a string.
     */
    public function getResolvedDirectives(): string
    {
        return $this->resolveDirectives($this->directives);
    }

    /**
     * Build the argument into a GraphQL string representation.
     */
    public function __toString(): string
    {
        $type = $this->returnType;

        return Str::of($this->name)
            ->append(': ')
            ->append(is_string($type) ? $type : $type->__toString())
            ->append($this->required ? '!' : '')
            ->append(' ')
            ->append($this->getResolvedDirectives())
            ->__toString();
    }
}
