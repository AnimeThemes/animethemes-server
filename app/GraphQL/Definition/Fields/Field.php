<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Concerns\GraphQL\ResolvesDirectives;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class Field.
 */
abstract class Field
{
    use ResolvesDirectives;

    /**
     * Create a new Field instance.
     *
     * @param  string  $column
     * @param  string|null  $name
     * @param  bool  $nullable
     */
    public function __construct(
        protected string $column,
        protected ?string $name = null,
        protected bool $nullable = true,
    ) {
    }

    /**
     * Get the name of the field.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?? Str::camel($this->column);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return '';
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function getType(): Type
    {
        if (!$this->nullable) {
            return Type::nonNull($this->type());
        }

        return $this->type();
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    abstract protected function type(): Type;

    /**
     * Resolve the field.
     *
     * @param  mixed  $root
     * @return mixed
     */
    public function resolve($root): mixed
    {
        return Arr::get($root, $this->column);
    }

    /**
     * Get the directives of the field.
     *
     * @return array
     */
    public function directives(): array
    {
        return [];
    }

    /**
     * Get the field as a string representation.
     *
     * @return string
     */
    public function toString(): String
    {
        $string = Str::of($this->getName())
            ->append(': ')
            ->append($this->getType()->toString());

        if ($this->getName() !== $this->column) {
            $string = $string->append(" @rename(attribute: {$this->column})");
        }

        if (filled($this->directives())) {
            $string = $string->append(' '.$this->resolveDirectives($this->directives()));
        }

        return $string->__toString();
    }
}
