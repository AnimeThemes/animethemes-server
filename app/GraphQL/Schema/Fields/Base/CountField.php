<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Schema\Fields\Field;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class CountField extends Field implements DisplayableField, SortableField
{
    public function __construct(
        protected string $relation,
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
        return SortType::COUNT_RELATION;
    }

    /**
     * The relation to sort the type.
     */
    public function relation(): ?string
    {
        return $this->relation;
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return $root->hasAttribute($attribute = "{$this->relation}_count")
            ? (int) $root->getAttribute($attribute)
            : $root->{$this->relation}->count();
    }
}
