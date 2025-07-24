<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Attributes\UseField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Resolvers\CountAggregateResolver;
use GraphQL\Type\Definition\Type;

#[UseField(CountAggregateResolver::class)]
class CountAggregateField extends Field implements DisplayableField
{
    public function __construct(
        protected string $aggregateRelation,
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
}
