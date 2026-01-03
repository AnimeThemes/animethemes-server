<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Relations;

use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Concerns\GraphQL\ResolvesArguments;
use App\Enums\GraphQL\PaginationType;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\FirstArgument;
use App\GraphQL\Argument\PageArgument;
use App\GraphQL\Argument\SortArgument;
use App\GraphQL\Criteria\Filter\FilterCriteria;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Schema\Unions\BaseUnion;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class Relation
{
    use FiltersModels;
    use ResolvesArguments;

    protected ?string $field = null;
    protected ?bool $nullable = true;
    protected Type $type;

    public function __construct(
        protected BaseType|BaseUnion $baseType,
        protected string $relationName,
    ) {
        $this->type = GraphQL::type($baseType->getName());
    }

    /**
     * Rename the relation to something else.
     */
    public function renameTo(string $name): static
    {
        $this->field = $name;

        return $this;
    }

    /**
     * Mark the relation as not nullable.
     */
    public function notNullable(): static
    {
        $this->nullable = false;

        return $this;
    }

    /**
     * Get the name used to query through the type.
     * By default, the relation name is used.
     */
    public function getName(): string
    {
        return $this->field ?? $this->relationName;
    }

    /**
     * Get the relation name in the model.
     */
    public function getRelationName(): string
    {
        return $this->relationName;
    }

    /**
     * Resolve the arguments of the sub-query.
     *
     * @return Argument[]
     */
    protected function arguments(): array
    {
        $arguments = [];

        $type = $this->baseType;

        if ($this->paginationType() !== PaginationType::NONE && $type instanceof BaseType) {
            $arguments[] = FilterCriteria::getFilters($type)
                ->map(fn (Filter $filter): Argument => $filter->argument());
        }

        if ($this->paginationType() !== PaginationType::NONE) {
            $arguments[] = new FirstArgument(true);
            $arguments[] = new PageArgument();

            if ($type instanceof BaseType) {
                $arguments[] = new SortArgument($type);
            }
        }

        return Arr::flatten($arguments);
    }

    public function getBaseType(): BaseType|BaseUnion
    {
        return $this->baseType;
    }

    /**
     * Get the pivot type if it exists.
     */
    public function getPivotType(): ?PivotType
    {
        return null;
    }

    /**
     * Resolve the relation.
     *
     * @param  array<string, mixed>  $args
     */
    public function resolve(Model $root, array $args): mixed
    {
        /** @var Collection $collection */
        $collection = $root->{$this->getRelationName()};

        $first = Arr::get($args, 'first');
        $page = Arr::get($args, 'page');

        return new LengthAwarePaginator(
            $collection->forPage($page, $first),
            $collection->count(),
            $first,
            $page
        );
    }

    abstract public function type(): Type;

    /**
     * The pagination type.
     */
    abstract public function paginationType(): PaginationType;
}
