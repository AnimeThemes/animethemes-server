<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Sort\Sort;

/**
 * Class BooleanField.
 */
abstract class BooleanField extends Field implements FilterableField, SelectableField, SortableField
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
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Criteria|null  $criteria
     * @return bool
     */
    public function shouldSelect(?Criteria $criteria): bool
    {
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
