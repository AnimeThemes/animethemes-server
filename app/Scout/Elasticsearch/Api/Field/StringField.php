<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\StringFilter;
use App\Http\Api\Sort\Sort;

/**
 * Class StringField.
 */
abstract class StringField extends Field implements FilterableField, SortableField
{
    /**
     * Create a new field instance.
     *
     * @param  string  $key
     * @param  string|null  $searchField
     */
    public function __construct(
        string $key,
        ?string $searchField = null
    ) {
        // String fields are not optimized for sorting so a multi-field mapping is required.
        // By default, we expect an unanalyzed keyword field 'sort' for sorting.
        parent::__construct($key, $searchField, "$key.sort");
    }

    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new StringFilter($this->getKey(), $this->getSearchField());
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
