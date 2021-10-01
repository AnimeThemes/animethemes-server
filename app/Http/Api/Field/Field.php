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
     * The key for the field.
     *
     * @var string
     */
    protected string $key;

    /**
     * The key for the field.
     *
     * @var string|null
     */
    protected ?string $column;

    /**
     * The category for the field.
     *
     * @var Category|null
     */
    protected ?Category $category;

    /**
     * Create a new field instance.
     *
     * @param  string  $key
     * @param  string|null  $column
     * @param  Category|null  $category
     */
    public function __construct(string $key, ?string $column = null, ?Category $category = null)
    {
        $this->key = $key;
        $this->category = $category;
        $this->column = $column;
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
