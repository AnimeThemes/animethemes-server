<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries;

use App\Concerns\GraphQL\ResolvesArguments;
use App\Concerns\GraphQL\ResolvesAttributes;
use App\Concerns\GraphQL\ResolvesDirectives;
use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Types\BaseType;
use GraphQL\Type\Definition\Type;

abstract class BaseQuery
{
    use ResolvesArguments;
    use ResolvesAttributes;
    use ResolvesDirectives;

    public function __construct(
        protected string $name,
        protected bool $nullable = false,
        protected bool $isList = true,
        protected bool $paginated = true,
    ) {}

    /**
     * Mount the query and return its string representation.
     */
    public function toGraphQLString(): string
    {
        $directives = $this->resolveDirectives($this->directives());

        $arguments = $this->buildArguments($this->arguments());

        return "
            \"\"\"{$this->description()}\"\"\"
            {$this->name}{$arguments}: {$this->getType()->__toString()} {$directives}
        ";
    }

    /**
     * The description of the query.
     */
    abstract public function description(): string;

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        $builder = $this->resolveBuilderAttribute();

        $field = $this->resolveFieldAttribute();

        return [
            ...(is_string($builder) ? ['builder' => ['method' => $builder]] : []),

            ...(is_string($field) ? ['field' => ['resolver' => $field]] : []),

            ...($this->paginated ? ['paginate' => []] : []),
        ];
    }

    /**
     * The arguments of the type.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        $arguments = [];
        $baseType = $this->baseType();

        if ($baseType instanceof BaseType && $baseType instanceof HasFields) {
            $arguments[] = $this->resolveFilterArguments($baseType->fields());
        }

        return $arguments;
    }

    /**
     * The type returned by the field.
     */
    public function getType(): Type
    {
        if (! $this->nullable) {
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
     */
    abstract public function baseType(): Type;

    /**
     * Get the name of the query.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
