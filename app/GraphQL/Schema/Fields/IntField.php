<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\GraphQL\Criteria\Sort\Sort;
use App\GraphQL\Filter\IntFilter;
use GraphQL\Type\Definition\Type;

abstract class IntField extends Field implements DisplayableField, FilterableField, SortableField
{
    public function baseType(): Type
    {
        return Type::int();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }

    public function getFilter(): IntFilter
    {
        return new IntFilter($this->getName(), $this->getColumn())
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
