<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries;

use App\Concerns\GraphQL\ResolvesArguments;
use App\Concerns\GraphQL\ResolvesAttributes;
use App\Concerns\GraphQL\ResolvesDirectives;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\WhereArgument;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;

abstract class BaseQuery
{
    use ResolvesArguments;
    use ResolvesAttributes;
    use ResolvesDirectives;

    public function __construct(
        protected string $name,
        protected bool $nullable = false,
        protected bool $isList = true,
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
        $paginate = $this->resolvePaginateAttribute();

        return [
            ...($this->resolveAllAttribute() ? ['all' => []] : []),

            ...($this->resolveAuthAttribute() ? ['auth' => []] : []),

            ...(is_string($builder) ? ['builder' => ['method' => $builder]] : []),

            ...(is_string($field) ? ['field' => ['resolver' => $field]] : []),

            ...($this->resolveFindAttribute() ? ['find' => []] : []),

            ...($this->resolveFirstAttribute() ? ['first' => []] : []),

            ...(is_bool($paginate) && $paginate ? ['paginate' => []] : []),
        ];
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];
        $baseType = $this->baseType();

        if ($this->resolveSearchAttribute()) {
            $arguments[] = new Argument('search', Type::string())->directives(['search' => []]);
        }

        if ($baseType instanceof BaseType) {
            $arguments[] = $this->resolveFilterArguments($baseType->fields());
        }

        if ($baseType instanceof BaseType && $this->resolvePaginateAttribute()) {
            $arguments[] = $this->resolveSortArguments($baseType);
        }

        if ($baseType instanceof BaseType) {
            $arguments[] = new WhereArgument($baseType);
        }

        return Arr::flatten($arguments);
    }

    /**
     * The type returned by the field.
     */
    public function getType(): Type
    {
        if (! $this->nullable) {
            if ($this->isList) {
                return Type::nonNull(Type::listOf(Type::nonNull($this->baseType())));
            }

            return Type::nonNull($this->baseType());
        }

        if ($this->isList) {
            return Type::listOf(Type::nonNull($this->baseType()));
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
