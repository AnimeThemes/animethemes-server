<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Concerns\GraphQL\ResolvesArguments;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\FirstArgument;
use App\GraphQL\Argument\PageArgument;
use App\GraphQL\Argument\SortArgument;
use App\GraphQL\Criteria\Filter\FilterCriteria;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Middleware\ResolveInfoMiddleware;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\BaseType;
use GraphQL\Type\Definition\Type;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

abstract class BaseQuery extends Query
{
    use ConstrainsEagerLoads;
    use FiltersModels;
    use ResolvesArguments;

    protected Response $response;

    public function __construct(
        protected string $name,
        protected bool $nullable = true,
        protected bool $isList = false,
    ) {
        $this->middleware = array_merge(
            $this->getMiddleware(),
            [
                ResolveInfoMiddleware::class,
            ]
        );
    }

    public function getAuthorizationMessage(): string
    {
        return $this->response->message() ?? 'Unauthorized';
    }

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
            $arguments[] = FilterCriteria::getFilters($baseType)->map(fn (Filter $filter): Argument => $filter->argument());
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
