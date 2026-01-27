<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base\Aggregate;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\Field\AggregateFunction;
use App\Enums\GraphQL\Filter\Clause;
use App\GraphQL\Filter\IntFilter;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class CountAggregateField extends AggregateField implements DisplayableField, SortableField
{
    public function __construct(
        public string $aggregateRelation,
        string $name,
        protected bool $nullable = false,
    ) {
        parent::__construct($aggregateRelation, $name, AggregateFunction::SUM, 'value', $nullable);
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

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return (int) parent::resolve(...func_get_args());
    }
}
