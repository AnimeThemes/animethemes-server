<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Http\Api\Filter\DateFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Sort\Sort;

/**
 * Class DateField.
 */
abstract class DateField extends Field implements FilterableField, SortableField
{
    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new DateFilter($this->getKey(), $this->getSearchField());
    }

    /**
     * Get the sort that can be applied to the field.
     *
     * @return Sort
     */
    public function getSort(): Sort
    {
        return new Sort($this->getKey(), $this->getSortField());
    }
}
