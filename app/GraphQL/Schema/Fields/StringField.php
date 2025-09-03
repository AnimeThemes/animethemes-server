<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Support\Filter\EqFilter;
use App\GraphQL\Support\Filter\Filter;
use App\GraphQL\Support\Filter\LikeFilter;
use GraphQL\Type\Definition\Type;

abstract class StringField extends Field implements DisplayableField, FilterableField, SortableField
{
    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::string();
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
            new LikeFilter($this),
        ];
    }

    public function sortType(): SortType
    {
        return SortType::ROOT;
    }
}
