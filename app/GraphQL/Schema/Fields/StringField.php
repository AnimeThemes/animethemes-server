<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\GraphQL\Criteria\Sort\Sort;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\StringFilter;
use GraphQL\Type\Definition\Type;

abstract class StringField extends Field implements DisplayableField, FilterableField, SortableField
{
    public function baseType(): Type
    {
        return Type::string();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }

    public function getFilter(): Filter
    {
        return new StringFilter($this->getName(), $this->getColumn())
            ->useEq()
            ->useLike();
    }

    public function getSort(): Sort
    {
        return new Sort($this->getName(), $this->getColumn());
    }
}
