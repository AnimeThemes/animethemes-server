<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Attributes\UseFieldDirective;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Resolvers\CountAggregateResolver;
use GraphQL\Type\Definition\Type;

#[UseFieldDirective(CountAggregateResolver::class)]
class CountAggregateField extends Field implements DisplayableField, SortableField
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
     * The sort type of the field.
     */
    public function sortType(): SortType
    {
        return SortType::AGGREGATE;
    }

    /**
     * The sort type of the field.
     */
    public function relation(): ?string
    {
        return $this->aggregateRelation;
    }
}
