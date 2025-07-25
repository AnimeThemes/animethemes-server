<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\OrderableField;
use App\Enums\GraphQL\OrderType;
use App\GraphQL\Attributes\UseField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Resolvers\CountAggregateResolver;
use GraphQL\Type\Definition\Type;

#[UseField(CountAggregateResolver::class)]
class CountAggregateField extends Field implements DisplayableField, OrderableField
{
    public function __construct(
        public string $aggregateRelation,
        protected string $column,
        protected ?string $name = null,
        protected bool $nullable = false,
    ) {
        parent::__construct($column, $name, $nullable);
    }

    /**
     * Get the directives of the field.
     *
     * @return array
     */
    public function directives(): array
    {
        return [
            'with' => [
                'relation' => $this->aggregateRelation,
            ],
            ...parent::directives(),
        ];
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::int();
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }

    /**
     * The order type of the field.
     */
    public function orderType(): OrderType
    {
        return OrderType::AGGREGATE;
    }

    /**
     * The order type of the field.
     */
    public function relation(): ?string
    {
        return $this->aggregateRelation;
    }
}
