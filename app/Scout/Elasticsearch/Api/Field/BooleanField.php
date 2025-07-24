<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Sort\Sort;

abstract class BooleanField extends Field implements FilterableField, SortableField
{
    /**
     * Get the filter that can be applied to the field.
     */
    public function getFilter(): Filter
    {
        return new BooleanFilter($this->getKey(), $this->getSearchField());
    }

    /**
     * Get the sort that can be applied to the field.
     */
    public function getSort(): Sort
    {
        return new Sort($this->getKey(), $this->getSortField());
    }
}
