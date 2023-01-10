<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\QualifyColumn;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AggregateField.
 */
abstract class AggregateField extends Field implements SortableField
{
    /**
     * Create a new field instance.
     *
     * @param  string  $key
     */
    public function __construct(string $key)
    {
        parent::__construct($key);
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
     * @param  Criteria|null  $criteria
     * @return bool
     */
    public function shouldAggregate(?Criteria $criteria): bool
    {
        return $criteria !== null && $criteria->isAllowedField($this->getKey());
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
