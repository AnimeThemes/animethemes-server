<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Relations;

use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Enums\GraphQL\PaginationType;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\FirstArgument;
use App\GraphQL\Argument\PageArgument;
use App\GraphQL\Argument\SortArgument;
use App\GraphQL\Criteria\Filter\FilterCriteria;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Schema\Unions\BaseUnion;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class Relation extends Field
{
    use FiltersModels;

    protected bool $asPivot = false;

    public function __construct(
        protected BaseType|BaseUnion $relatedType,
        protected string $relationName,
    ) {
        parent::__construct($relationName);
    }

    /**
     * Rename the relation to something else.
     */
    public function renameTo(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Mark the relation as non-nullable.
     */
    public function nonNullable(): static
    {
        $this->nullable = false;

        return $this;
    }

    /**
     * Mark the relation as a relation of the pivot model.
     */
    public function asPivot(): static
    {
        $this->asPivot = true;

        return $this;
    }

    /**
     * Get the relation name in the model.
     */
    public function getRelationName(): string
    {
        return $this->getColumn();
    }

    public function isPivot(): bool
    {
        return $this->asPivot;
    }

    /**
     * Resolve the arguments of the sub-query.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];

        $type = $this->relatedType;

        if ($this->paginationType() !== PaginationType::NONE && $type instanceof BaseType) {
            $arguments[] = FilterCriteria::getFilters($type)
                ->map(fn (Filter $filter): array => $filter->getArguments())
                ->flatten();
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

    /**
     * @return BaseType|BaseUnion
     */
    public function baseType()
    {
        return $this->relatedType;
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
     * @param  Model  $root
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        /** @var Collection $collection */
        $collection = $root->{$this->getRelationName()};

        $first = max(1, Arr::integer($args, 'first'));
        $page = Arr::get($args, 'page');

        return new LengthAwarePaginator(
            $collection->forPage($page, $first),
            $collection->count(),
            $first,
            $page
        );
    }

    /**
     * The pagination type.
     */
    abstract public function paginationType(): PaginationType;
}
