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

    public function getFilter(): Filter
    {
        return new EnumFilter($this->getKey(), $this->enumClass, $this->getColumn());
    }

    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    public function render(Model $model): ?string
    {
        $enum = $model->getAttribute($this->getColumn());

        return $enum?->localize();
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    public function getSort(): Sort
    {
        return new Sort($this->getKey(), $this->getColumn());
    }
}
