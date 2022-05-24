<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\BaseEnum;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Sort\Sort;

/**
 * Class EnumField.
 */
abstract class EnumField extends Field implements FilterableField, SortableField
{
    /**
     * Create a new field instance.
     *
     * @param  string  $key
     * @param  class-string<BaseEnum>  $enumClass
     * @param  string|null  $column
     */
    public function __construct(
        string $key,
        protected readonly string $enumClass,
        ?string $column = null
    ) {
        parent::__construct($key, $column);
    }

    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new EnumFilter($this->getKey(), $this->enumClass, $this->getSearchField());
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
