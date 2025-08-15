<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Support\Filter\EqFilter;
use App\GraphQL\Support\Filter\Filter;
use App\GraphQL\Support\Filter\InFilter;
use App\GraphQL\Support\Filter\NotInFilter;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class EnumField extends Field implements DisplayableField, FilterableField, SortableField
{
    public function __construct(
        public string $column,
        public string $enum,
        public ?string $name = null,
        public bool $nullable = true,
    ) {
        parent::__construct($column, $name, $nullable);
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return GraphQL::type(class_basename($this->enum));
    }

    /**
     * Resolve the field.
     */
    public function resolve(mixed $root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return Arr::get($root, $this->column)?->name;
    }

    /**
     * The filters of the field.
     *
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return [
            new EqFilter($this),
            new InFilter($this),
            new NotInFilter($this),
        ];
    }

    /**
     * The sort type of the field.
     */
    public function sortType(): SortType
    {
        return SortType::ROOT;
    }
}
