<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Schema\Fields\Field;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;

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

    public function baseType(): Type
    {
        return Type::int();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }

    public function sortType(): SortType
    {
        return SortType::AGGREGATE;
    }

    /**
     * The relation to sort the type.
     */
    public function relation(): ?string
    {
        return $this->aggregateRelation;
    }

    /**
     * Resolve the field.
     *
     * @param  Model  $root
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return (int) $root->{$this->aggregateRelation}?->value;
    }
}
