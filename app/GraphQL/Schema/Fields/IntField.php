<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Filter\IntFilter;
use GraphQL\Type\Definition\Type;

abstract class IntField extends Field implements DisplayableField, FilterableField
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
        return new IntFilter($this->name(), $this->getColumn())
            ->useEq()
            ->useLt()
            ->useGt()
            ->useIn()
            ->useNotIn();
    }
}
