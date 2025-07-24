<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Enums\Http\Api\Field\AggregateFunction;
use App\Enums\Http\Api\QualifyColumn;
use App\Http\Api\Field\Aggregate\AggregateField;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\IntFilter;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Sort\Sort;
use App\Models\Aggregate\ViewAggregate;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Model;

class VideoViewCountField extends AggregateField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::RELATION_VIEW_AGGREGATE, AggregateFunction::SUM, ViewAggregate::ATTRIBUTE_VALUE);
    }

    /**
     * Get the filters that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new IntFilter(key: $this->alias(), column: ViewAggregate::ATTRIBUTE_VALUE, qualifyColumn: QualifyColumn::YES);
    }

    /**
     * Get the sort that can be applied to the field.
     *
     * @return Sort
     */
    public function getSort(): Sort
    {
        return new Sort(key: $this->alias(), column: 'view_aggregate_sum_value', qualifyColumn: QualifyColumn::NO);
    }

    /**
     * Get the value to display to the user.
     *
     * @param  Model  $model
     * @return mixed
     */
    public function render(Model $model): mixed
    {
        return (int) $model->getAttribute('view_aggregate_sum_value');
    }

    /**
     * Format the aggregate value to its sub-select alias / model attribute.
     *
     * @return string
     */
    public function alias(): string
    {
        return 'views_count';
    }
}
