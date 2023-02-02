<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Enums\Http\Api\Filter\Clause;
use App\Enums\Http\Api\QualifyColumn;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\IntFilter;
use App\Http\Api\Schema\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class CountField.
 */
abstract class CountField extends AggregateField
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
     * Get the filters that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new IntFilter($this->getKey(), static::format($this->getKey()), QualifyColumn::NO(), Clause::HAVING());
    }

    /**
     * Load the aggregate field value for the model.
     *
     * @param  Model  $model
     * @return Model
     */
    public function load(Model $model): Model
    {
        return $model->loadCount($this->getKey());
    }

    /**
     * Eager load the aggregate value for the query builder.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    public function with(Builder $builder): Builder
    {
        return $builder->withCount($this->getKey());
    }

    /**
     * Format the aggregate value to its sub-select alias / model attribute.
     *
     * @param  string  $key
     * @return string
     */
    public static function format(string $key): string
    {
        return Str::of($key)
            ->append('_count')
            ->__toString();
    }
}
