<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries;

use App\Concerns\GraphQL\ResolvesArguments;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\FirstArgument;
use App\GraphQL\Support\Argument\PageArgument;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

abstract class BaseQuery extends Query
{
    use ResolvesArguments;

    public function __construct(
        protected string $name,
        protected bool $nullable = true,
        protected bool $isList = false,
    ) {}

    /**
     * Get the attributes of the query.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->description(),
        ];
    }

    /**
     * Get the name of the query.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * The description of the type.
     */
    abstract public function description(): string;

    /**
     * The arguments of the class resolve as customs class helper.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];

        $baseType = $this->baseRebingType();

        if ($this instanceof EloquentPaginatorQuery) {
            $arguments[] = new FirstArgument();
            $arguments[] = new PageArgument();
        }

        if ($baseType instanceof BaseType) {
            $arguments[] = $this->resolveFilterArguments($baseType->fieldClasses());
        }

        if ($baseType instanceof BaseType && $this instanceof EloquentPaginatorQuery) {
            $arguments[] = $this->resolveSortArguments($baseType);
        }

        // if ($baseType instanceof BaseType && $this->resolvePaginateAttribute()) {
        //     $arguments[] = $this->resolveSortArguments($baseType);
        // }

        return Arr::flatten($arguments);
    }

    /**
     * Convert the rebing type to a GraphQL type.
     */
    public function baseType(): Type
    {
        return GraphQL::type($this->baseRebingType()->getName());
    }

    /**
     * The base return rebing type of the query.
     */
    public function baseRebingType(): ?BaseType
    {
        return null;
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
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
}
