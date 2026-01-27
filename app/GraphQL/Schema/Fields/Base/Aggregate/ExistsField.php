<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base\Aggregate;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Enums\GraphQL\Field\AggregateFunction;
use App\Enums\GraphQL\Filter\Clause;
use App\GraphQL\Filter\BooleanFilter;
use GraphQL\Type\Definition\Type;

class ExistsField extends AggregateField implements DisplayableField
{
    public function __construct(
        protected string $aggregateRelation,
        protected bool $nullable = false,
    ) {
        parent::__construct($aggregateRelation, $aggregateRelation.'Exists', AggregateFunction::EXISTS, '*', $nullable);
    }

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
        return new BooleanFilter($this->getName(), $this->alias(), Clause::HAVING);
    }
}
