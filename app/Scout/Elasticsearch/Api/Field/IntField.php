<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\IntFilter;
use App\Http\Api\Sort\Sort;

abstract class IntField extends Field implements FilterableField, SortableField
{
    public function getFilter(): Filter
    {
        return new IntFilter($this->getKey(), $this->getSearchField());
    }

    public function getSort(): Sort
    {
        return new Sort($this->getKey(), $this->getSortField());
    }
}
