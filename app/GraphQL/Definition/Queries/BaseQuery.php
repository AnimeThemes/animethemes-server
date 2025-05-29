<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries;

use App\Concerns\GraphQL\ResolvesDirectives;
use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\BaseType;
use GraphQL\Type\Definition\Type;

/**
 * Class BaseQuery.
 */
abstract class BaseQuery
{
    use ResolvesDirectives;

    /**
     * @param  bool  $nullable
     * @param  bool  $isList
     */
    public function __construct(
        protected string $name,
        protected bool $nullable = false,
        protected bool $isList = true,
        protected bool $paginated = true,
    ) {
    }

    /**
     * Mount the query and return its string representation.
     *
     * @return string
     */
    public function mount(): string
    {
        $directives = filled($this->directives()) ? $this->resolveDirectives($this->directives()) : '';

        $argumentsString = filled($this->arguments())
            ? '('.implode("\n                ", $this->arguments()).')'
            : '';

        return "
            \"\"\"{$this->description()}\"\"\"
            {$this->name}{$argumentsString}: {$this->getType()->toString()} {$directives}
        ";
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    abstract public function description(): string;

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            ...($this->paginated ? ['paginate' => []] : []),
        ];
    }

    /**
     * The arguments of the type.
     *
     * @return array<int, string>
     */
    public function arguments(): array
    {
        $baseType = $this->baseType();

        if ($baseType instanceof BaseType && filled($baseType->fields())) {
            return collect($baseType->fields())
                ->map(function (Field $field) {
                    if ($field instanceof FilterableField) {
                        return $field->getFilter()->toString();
                    }
                })
                ->toArray();
        }

        return [];
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function getType(): Type
    {
        if (!$this->nullable) {
            if ($this->isList) {
                return Type::listOf(Type::nonNull($this->baseType()));
            }

            return Type::nonNull($this->baseType());
        }

        if ($this->isList) {
            return Type::listOf($this->baseType());
        }

        return $this->baseType();
    }

    /**
     * The base return type of the query.
     *
     * @return Type
     */
    abstract public function baseType(): Type;
}
