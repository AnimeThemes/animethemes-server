<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\GraphQL\Filter\EnumFilter;
use App\GraphQL\Sort\Sort;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;
use UnitEnum;

abstract class EnumField extends Field implements DisplayableField, FilterableField, SortableField
{
    /**
     * @param  class-string<UnitEnum>  $enum
     */
    public function __construct(
        public string $column,
        public string $enum,
        public ?string $name = null,
        public bool $nullable = true,
    ) {
        parent::__construct($column, $name, $nullable);
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }

    public function baseType(): Type
    {
        return GraphQL::type(class_basename($this->enum));
    }

    public function resolve(mixed $root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return Arr::get($root, $this->column)?->name;
    }

    public function getFilter(): EnumFilter
    {
        return new EnumFilter($this->getName(), $this->enum, $this->getColumn())
            ->useEq()
            ->useIn()
            ->useNotIn();
    }

    public function getSort(): Sort
    {
        return new Sort($this->getName(), $this->getColumn());
    }
}
