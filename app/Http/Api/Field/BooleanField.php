<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\RenderableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BooleanField.
 */
abstract class BooleanField extends Field implements FilterableField, RenderableField, SelectableField, SortableField
{
    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new BooleanFilter($this->getKey(), $this->getColumn());
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldRender(ReadQuery $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    /**
     * Get the value to display to the user.
     *
     * @param  Model  $model
     * @return mixed
     */
    public function render(Model $model): mixed
    {
        return $model->getAttribute($this->getColumn());
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldSelect(ReadQuery $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    /**
     * Get the sort that can be applied to the field.
     *
     * @return Sort
     */
    public function getSort(): Sort
    {
        return new Sort($this->getKey(), $this->getColumn());
    }
}
