<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Concerns\GraphQL\ResolvesArguments;
use App\Concerns\GraphQL\ResolvesAttributes;
use App\Concerns\GraphQL\ResolvesDirectives;
use App\Contracts\GraphQL\Fields\HasArgumentsField;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Stringable;

abstract class Field implements Stringable
{
    use ResolvesArguments;
    use ResolvesAttributes;
    use ResolvesDirectives;

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

    /**
     * Get the directives of the field.
     *
     * @return array
     */
    public function directives(): array
    {
        $deprecated = $this->resolveDeprecatedAttribute();
        $field = $this->resolveFieldAttribute();
        $paginate = $this->resolvePaginateAttribute();

        return [
            ...(is_string($deprecated) ? ['deprecated' => ['reason' => $deprecated]] : []),

            ...(is_string($field) ? ['field' => ['resolver' => $field]] : []),

            ...(is_array($paginate) ? ['paginate' => $paginate] : []),
        ];
    }

    /**
     * Get the field as a string representation.
     */
    public function __toString(): string
    {
        $string = Str::of($this->getName());

        if ($this instanceof HasArgumentsField) {
            $string = $string->append($this->buildArguments($this->arguments()));
        }

        $string = $string->append(': ')
            ->append($this->getType()->__toString());

        if ($this->shouldRename()) {
            $string = $string->append(" @rename(attribute: {$this->column})");
        }

        if (filled($this->directives())) {
            $string = $string->append(' '.$this->resolveDirectives($this->directives()));
        }

        return $string->__toString();
    }

    /**
     * Determine if the field is different from the column.
     */
    public function shouldRename(): bool
    {
        if (Arr::has($this->directives(), 'field')) {
            return false;
        }

        return $this->getName() !== $this->column;
    }
}
