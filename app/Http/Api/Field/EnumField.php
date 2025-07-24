<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\RenderableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Model;

abstract class EnumField extends Field implements FilterableField, RenderableField, SelectableField, SortableField
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
     * Get the enum class.
     *
     * @return class-string
     */
    public function getEnumClass(): string
    {
        return $this->enumClass;
    }

    /**
     * Get the filter that can be applied to the field.
     */
    public function getFilter(): Filter
    {
        return new EnumFilter($this->getKey(), $this->enumClass, $this->getColumn());
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    /**
     * Get the value to display to the user.
     */
    public function render(Model $model): ?string
    {
        $enum = $model->getAttribute($this->getColumn());

        return $enum?->localize();
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    /**
     * Get the sort that can be applied to the field.
     */
    public function getSort(): Sort
    {
        return new Sort($this->getKey(), $this->getColumn());
    }
}
