<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base\Aggregate;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\Field\AggregateFunction;
use App\Enums\GraphQL\Filter\Clause;
use App\GraphQL\Filter\IntFilter;
use GraphQL\Type\Definition\Type;

class CountField extends AggregateField implements DisplayableField, FilterableField, SortableField
{
    public function __construct(
        protected string $aggregateRelation,
        protected ?string $name = null,
        protected bool $nullable = false,
    ) {
        parent::__construct($aggregateRelation, $aggregateRelation.'Count', AggregateFunction::COUNT, '*', $nullable);
    }

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
        return new IntFilter($this->getName(), $this->alias(), Clause::HAVING);
    }
}
