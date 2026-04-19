<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Filter\FloatFilter;
use GraphQL\Type\Definition\Type;

abstract class FloatField extends Field implements DisplayableField, FilterableField
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
        return new FloatFilter($this->name(), $this->getColumn())
            ->useEq()
            ->useLt()
            ->useGt();
    }
}
