<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\BaseEnum;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Sort\Sort;

/**
 * Class EnumField.
 */
abstract class EnumField extends Field implements FilterableField, SelectableField, SortableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     * @param  string  $key
     * @param  class-string<BaseEnum>  $enumClass
     * @param  string|null  $column
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
     * Get the enum class.
     *
     * @return class-string<BaseEnum>
     */
    public function getEnumClass(): string
    {
        return $this->enumClass;
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

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldSelect(ReadQuery $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

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
