<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\GraphQL\Filter\FloatFilter;
use App\GraphQL\Sort\Sort;
use GraphQL\Type\Definition\Type;

abstract class FloatField extends Field implements DisplayableField, FilterableField, SortableField
{
    public function baseType(): Type
    {
        return Type::float();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }

    public function getFilter(): FloatFilter
    {
        return new FloatFilter($this->getName(), $this->getColumn())
            ->useEq()
            ->useLt()
            ->useGt()
            ->useIn()
            ->useNotIn();
    }

    public function getSort(): Sort
    {
        return new Sort($this->getName(), $this->getColumn());
    }
}
