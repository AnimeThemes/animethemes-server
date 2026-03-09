<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Concerns\GraphQL\ResolvesArguments;
use App\Contracts\GraphQL\Fields\DeprecatedField;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\SortArgument;
use App\GraphQL\Criteria\Filter\FilterCriteria;
use App\GraphQL\Filter\Filter;
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
        protected bool $nullable = true,
        protected bool $isList = false,
    ) {}

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
            'name' => $this->name(),
            'description' => $this->description(),
            'baseType' => $this->baseType(),
            'deprecationReason' => $this instanceof DeprecatedField ? $this->deprecationReason() : null,
        ];
    }

    abstract public function name(): string;

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

        if ($baseType->hasFilterableColumns()) {
            $arguments[] = FilterCriteria::getFilters($baseType)->map(fn (Filter $filter): array => $filter->getArguments())->flatten();
        }

        if ($baseType->hasSortableColumns()) {
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
    abstract public function baseType(): BaseType;

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
