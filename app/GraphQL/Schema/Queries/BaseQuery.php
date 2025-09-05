<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Concerns\GraphQL\ResolvesArguments;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\FirstArgument;
use App\GraphQL\Support\Argument\PageArgument;
use App\GraphQL\Support\Argument\SortArgument;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

abstract class BaseQuery extends Query
{
    use ConstrainsEagerLoads;
    use FiltersModels;
    use ResolvesArguments;

    public function __construct(
        protected string $name,
        protected bool $nullable = true,
        protected bool $isList = false,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->description(),
            'baseType' => $this->baseType(),
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function description(): string;

    /**
     * The arguments of the class resolve as customs class helper.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];

        $baseType = $this->baseType();

        if ($this instanceof EloquentPaginationQuery) {
            $arguments[] = new FirstArgument();
            $arguments[] = new PageArgument();
        }

        if ($baseType instanceof BaseType) {
            $arguments[] = $this->resolveFilterArguments($baseType->fieldClasses());
        }

        if ($baseType instanceof BaseType && $this instanceof EloquentPaginationQuery) {
            $arguments[] = new SortArgument($baseType);
        }

        return Arr::flatten($arguments);
    }

    /**
     * Convert the rebing type to a GraphQL type.
     */
    public function toType(): Type
    {
        return GraphQL::type($this->baseType()->getName());
    }

    /**
     * The base return rebing type of the query.
     */
    public function baseType(): ?BaseType
    {
        return null;
    }

    public function type(): Type
    {
        if (! $this->nullable) {
            if ($this->isList) {
                return Type::nonNull(Type::listOf(Type::nonNull($this->toType())));
            }

            return Type::nonNull($this->toType());
        }

        if ($this->isList) {
            return Type::listOf(Type::nonNull($this->toType()));
        }

        return $this->toType();
    }
}
