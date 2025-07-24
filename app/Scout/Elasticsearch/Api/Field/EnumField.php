<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Sort\Sort;
use App\Scout\Elasticsearch\Api\Schema\Schema;

abstract class EnumField extends Field implements FilterableField, SortableField
{
    /**
     * @param  class-string  $enumClass
     */
    public function __construct(
        Schema $schema,
        string $key,
        protected readonly string $enumClass,
        ?string $column = null
    ) {
        parent::__construct($schema, $key, $column);
    }

    /**
     * Get the filter that can be applied to the field.
     */
    public function getFilter(): Filter
    {
        return new EnumFilter($this->getKey(), $this->enumClass, $this->getSearchField());
    }

    /**
     * Get the sort that can be applied to the field.
     */
    public function getSort(): Sort
    {
        return new Sort($this->getKey(), $this->getSortField());
    }
}
