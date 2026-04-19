<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\StringFilter;
use GraphQL\Type\Definition\Type;

abstract class StringField extends Field implements DisplayableField, FilterableField
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
        return new StringFilter($this->name(), $this->getColumn())
            ->useEq()
            ->useLike();
    }
}
