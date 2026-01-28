<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base\Aggregate;

use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\Field\AggregateFunction;
use App\Enums\GraphQL\QualifyColumn;
use App\GraphQL\Criteria\Filter\FilterCriteria;
use App\GraphQL\Criteria\Filter\WhereConditionsFilterCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\GraphQL\Schema\Enums\FilterableColumns;
use App\GraphQL\Schema\Enums\SortableColumns;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Sort\Sort;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

abstract class AggregateField extends Field implements FilterableField, SortableField
{
    private Collection $filterableFields;

    public function __construct(
        public string $relation,
        protected string $fieldName,
        protected AggregateFunction $function,
        protected string $aggregateColumn,
        bool $nullable = true
    ) {
        parent::__construct($this->alias(), $fieldName, $nullable);
    }

    public function getSort(): Sort
    {
        return new Sort($this->fieldName, $this->alias(), qualifyColumn: QualifyColumn::NO);
    }

    /**
     * Eager load the aggregate value for the query builder.
     */
    public function shouldAggregate(array $args, array $selection, BaseType $type): bool
    {
        // If the field is being requested.
        if (Arr::has($selection, $this->getName())) {
            return true;
        }

        // If the sorting is requesting an aggregate function.
        /** @var SortCriteria[] $sortCriteria */
        $sortCriteria = Arr::get(new SortableColumns($type)->getAttributes(), 'criteria');
        $sorts = Arr::get($args, 'sort', []);
        foreach ($sortCriteria as $sortCriterion) {
            if (in_array($sortCriterion->__toString(), $sorts) && $sortCriterion->getField()->getName() === $this->getName()) {
                return true;
            }
        }

        // If the filtering is requesting an aggregate function.
        $filterCriteria = FilterCriteria::parse($type, $args);
        foreach ($filterCriteria as $filterCriterion) {
            if ($filterCriterion instanceof WhereConditionsFilterCriteria) {
                $this->filterableFields = new FilterableColumns($type)->getValues();
                foreach ($filterCriterion->getFilterValues() as $filterValue) {
                    if ($this->recursiveWhere($filterValue)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Eager load the aggregate value for the query builder.
     */
    public function with(Builder $builder): Builder
    {
        return $builder->withAggregate($this->relation, $this->aggregateColumn, $this->function->value);
    }

    /**
     * Eager load the aggregate value for the query builder.
     */
    public function load(Model $model): Model
    {
        return $model->loadAggregate($this->relation, $this->aggregateColumn, $this->function->value);
    }

    /**
     * Resolve the field.
     *
     * @param  Model  $root
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return $root->getAttribute($this->alias());
    }

    public function alias(): string
    {
        return Str::of($this->relation)
            ->snake()
            ->append('_')
            ->append($this->function->value)
            ->when($this->aggregateColumn !== '*', fn (Stringable $string) => $string->append('_')->append($this->aggregateColumn))
            ->__toString();
    }

    private function recursiveWhere(array $value): bool
    {
        $fieldName = Arr::get($value, 'field');
        $fieldValue = Arr::get($value, 'value');

        if (filled($fieldName) && filled($fieldValue)) {
            $field = $this->filterableFields->get($fieldName);
            if ($field instanceof Field && $field->getName() === $this->getName()) {
                return true;
            }
        }

        if ($and = Arr::get($value, 'AND')) {
            foreach ($and as $andSolo) {
                if ($this->recursiveWhere($andSolo)) {
                    return true;
                }
            }
        }

        if ($or = Arr::get($value, 'OR')) {
            foreach ($or as $orSolo) {
                if ($this->recursiveWhere($orSolo)) {
                    return true;
                }
            }
        }

        return false;
    }
}
