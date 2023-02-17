<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\RenderableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\QualifyColumn;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\ScopeParser;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AggregateField.
 */
abstract class AggregateField extends Field implements FilterableField, RenderableField, SortableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     * @param  string  $key
     */
    public function __construct(Schema $schema, string $key)
    {
        parent::__construct($schema, $key);
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
        return $model->getAttribute(static::format($this->getColumn()));
    }

    /**
     * Get the sort that can be applied to the field.
     *
     * @return Sort
     */
    public function getSort(): Sort
    {
        return new Sort($this->getKey(), static::format($this->getKey()), QualifyColumn::NO());
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
     * @param  Model  $model
     * @return Model
     */
    abstract public function load(Model $model): Model;

    /**
     * Eager load the aggregate value for the query builder.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    abstract public function with(Builder $builder): Builder;

    /**
     * Format the aggregate value to its sub-select alias / model attribute.
     *
     * @param  string  $key
     * @return string
     */
    abstract public static function format(string $key): string;
}
