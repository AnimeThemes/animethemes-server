<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Enums\Http\Api\Field\Category;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Sort\Sort;

/**
 * Class Field.
 */
abstract class Field
{
    /**
     * Create a new field instance.
     *
     * @param  string  $key
     * @param  string|null  $column
     * @param  Category|null  $category
     */
    public function __construct(
        protected string $key,
        protected ?string $column = null,
        protected ?Category $category = null
    ) {
    }

    /**
     * Get the field key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the field column.
     *
     * @return string|null
     */
    public function getColumn(): ?string
    {
        return $this->column;
    }

    /**
     * Get the field category.
     *
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category ?? Category::ATTRIBUTE();
    }

    /**
     * Get the filters that can be applied to the field.
     *
     * @return Filter
     */
    abstract public function getFilter(): Filter;

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
