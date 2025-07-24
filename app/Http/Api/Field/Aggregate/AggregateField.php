<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Aggregate;

use App\Concerns\Actions\Http\Api\FiltersModels;
use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\RenderableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Field\AggregateFunction;
use App\Enums\Http\Api\QualifyColumn;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\ScopeParser;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class AggregateField extends Field implements FilterableField, RenderableField, SortableField
{
    use FiltersModels;

    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     * @param  string  $relation
     * @param  AggregateFunction  $function
     * @param  string  $aggregateColumn
     */
    public function __construct(
        Schema $schema,
        protected readonly string $relation,
        protected readonly AggregateFunction $function,
        protected readonly string $aggregateColumn
    ) {
        parent::__construct($schema, $this->alias());
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @param  Query  $query
     * @return bool
     */
    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria !== null && $criteria->isAllowedField($this->getKey());
    }

    /**
     * Get the value to display to the user.
     *
     * @param  Model  $model
     * @return mixed
     */
    public function render(Model $model): mixed
    {
        return $model->getAttribute($this->alias());
    }

    /**
     * Get the sort that can be applied to the field.
     *
     * @return Sort
     */
    public function getSort(): Sort
    {
        return new Sort(key: $this->alias(), qualifyColumn: QualifyColumn::NO);
    }

    /**
     * Determine if the aggregate value should be included in the select clause of our query.
     *
     * @param  Query  $query
     * @return bool
     */
    public function shouldAggregate(Query $query): bool
    {
        // Select aggregate if explicitly included in sparse fieldsets
        $fieldCriteria = $query->getFieldCriteria($this->schema->type());
        if ($fieldCriteria !== null && $fieldCriteria->isAllowedField($this->getKey())) {
            return true;
        }

        $scope = ScopeParser::parse($this->schema->type());

        // Select aggregate if filtering on the aggregate value
        $filter = $this->getFilter();
        foreach ($query->getFilterCriteria() as $criteria) {
            if ($criteria->shouldFilter($filter, $scope)) {
                return true;
            }
        }

        // Select aggregate if sorting on the aggregate value
        $sort = $this->getSort();
        foreach ($query->getSortCriteria() as $sortCriterion) {
            if ($sortCriterion->shouldSort($sort, $scope)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load the aggregate field value for the model.
     *
     * @param  Query  $query
     * @param  Model  $model
     * @return Model
     */
    public function load(Query $query, Model $model): Model
    {
        $constrainedRelation = [];

        $relationSchema = $this->schema->relation($this->relation);
        $constrainedRelation[$this->relation] = function (Builder $relationBuilder) use ($query, $relationSchema) {
            if ($relationSchema !== null) {
                // TODO: distinguish scope from type
                $scope = ScopeParser::parse($this->relation);
                $this->filter($relationBuilder, $query, $relationSchema, $scope);
            }
        };

        return $model->loadAggregate($constrainedRelation, $this->aggregateColumn, $this->function->value);
    }

    /**
     * Eager load the aggregate value for the query builder.
     *
     * @param  Query  $query
     * @param  Builder  $builder
     * @return Builder
     */
    public function with(Query $query, Builder $builder): Builder
    {
        $constrainedRelation = [];

        $relationSchema = $this->schema->relation($this->relation);
        $constrainedRelation[$this->relation] = function (Builder $relationBuilder) use ($query, $relationSchema) {
            if ($relationSchema !== null) {
                // TODO: distinguish scope from type
                $scope = ScopeParser::parse($this->relation);
                $this->filter($relationBuilder, $query, $relationSchema, $scope);
            }
        };

        return $builder->withAggregate($constrainedRelation, $this->aggregateColumn, $this->function->value);
    }

    /**
     * Format the aggregate value to its sub-select alias / model attribute.
     *
     * @return string
     */
    public function alias(): string
    {
        return Str::of($this->relation)
            ->append('_')
            ->append($this->function->value)
            ->__toString();
    }
}
