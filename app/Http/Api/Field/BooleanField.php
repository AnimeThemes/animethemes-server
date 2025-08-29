<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\RenderableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Model;

abstract class BooleanField extends Field implements FilterableField, RenderableField, SelectableField, SortableField
{
    /**
     * Get the filter that can be applied to the field.
     */
    public function getFilter(): Filter
    {
        return new BooleanFilter($this->getKey(), $this->getColumn());
    }

    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    public function render(Model $model): mixed
    {
        return $model->getAttribute($this->getColumn());
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
