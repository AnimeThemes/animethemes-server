<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Filter\EqFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\GreaterFilter;
use App\GraphQL\Filter\InFilter;
use App\GraphQL\Filter\LesserFilter;
use App\GraphQL\Filter\NotInFilter;
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

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return [
            new EqFilter($this),
            new InFilter($this),
            new NotInFilter($this),
            new LesserFilter($this),
            new GreaterFilter($this),
        ];
    }

    public function sortType(): SortType
    {
        return SortType::ROOT;
    }
}
