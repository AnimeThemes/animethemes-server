<?php

declare(strict_types=1);

namespace App\GraphQL\Support;

use App\Concerns\GraphQL\ResolvesDirectives;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable as SupportStringable;
use Stringable;

final class Argument implements Stringable
{
    use ResolvesDirectives;

    protected bool $required = false;

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
     * Set the directives of the argument.
     *
     * @param  array<string, array>  $directives
     */
    public function directives(array $directives = []): static
    {
        $this->directives = $directives;

        return $this;
    }

    /**
     * Get the resolved directives as a string.
     */
    protected function getResolvedDirectives(): string
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
            ->when($this->required, fn (SupportStringable $string) => $string->append('!'))
            ->append(' ')
            ->append($this->getResolvedDirectives())
            ->__toString();
    }
}
