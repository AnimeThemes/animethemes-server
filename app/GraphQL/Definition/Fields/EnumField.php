<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\OrderableField;
use App\Enums\GraphQL\OrderType;
use App\GraphQL\Definition\Directives\Filters\EqFilterDirective;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Directives\Filters\InFilterDirective;
use App\GraphQL\Definition\Directives\Filters\NotInFilterDirective;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Schema\TypeRegistry;

abstract class EnumField extends Field implements DisplayableField, FilterableField, OrderableField
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
    public function type(): Type
    {
        return app(TypeRegistry::class)->get(class_basename($this->enum));
    }

    /**
     * Get the directives of the field.
     *
     * @return array
     */
    public function directives(): array
    {
        return [
            'enumField' => [],
        ];
    }

    /**
     * Resolve the field.
     */
    public function resolve(mixed $root): mixed
    {
        return Arr::get($root, $this->column)?->name;
    }

    /**
     * The directives available for this filter.
     *
     * @return FilterDirective[]
     */
    public function filterDirectives(): array
    {
        return [
            new EqFilterDirective($this, $this->type()),
            new InFilterDirective($this, $this->type()),
            new NotInFilterDirective($this, $this->type()),
        ];
    }

    /**
     * The order type of the field.
     */
    public function orderType(): OrderType
    {
        return OrderType::ROOT;
    }
}
