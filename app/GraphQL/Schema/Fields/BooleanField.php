<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Filter\BooleanFilter;
use GraphQL\Type\Definition\Type;

abstract class BooleanField extends Field implements DisplayableField, FilterableField, SortableField
{
    public function baseType(): Type
    {
        return Type::boolean();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }

    public function getFilter(): BooleanFilter
    {
        return new BooleanFilter($this->getName(), $this->getColumn())
            ->useEq();
    }

    public function sortType(): SortType
    {
        return SortType::ROOT;
    }
}
