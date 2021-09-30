<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Enums\BaseEnum;
use App\Enums\Http\Api\Field\Category;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\Filter\Filter;

/**
 * Class EnumField.
 */
class EnumField extends Field
{
    /**
     * The Enum class string.
     *
     * @var class-string<BaseEnum>
     */
    protected string $enumClass;

    /**
     * Create a new field instance.
     *
     * @param string $key
     * @param  class-string<BaseEnum>  $enumClass
     * @param string|null $column
     * @param Category|null $category
     */
    public function __construct(string $key, string $enumClass, ?string $column = null, ?Category $category = null)
    {
        parent::__construct($key, $column, $category);

        $this->enumClass = $enumClass;
    }

    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new EnumFilter($this->getKey(), $this->enumClass, $this->getColumn());
    }
}
