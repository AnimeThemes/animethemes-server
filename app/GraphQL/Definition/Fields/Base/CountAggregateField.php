<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Resolvers\CountAggregateResolver;
use GraphQL\Type\Definition\Type;

/**
 * Class CountAggregateField.
 */
class CountAggregateField extends Field
{
    /**
     * Create a new Field instance.
     *
     * @param  string  $aggregateRelation
     * @param  string  $column
     * @param  string|null  $name
     * @param  bool  $nullable
     */
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
            'field' => [
                'resolver' => CountAggregateResolver::class,
            ],
        ];
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        return Type::int();
    }
}
