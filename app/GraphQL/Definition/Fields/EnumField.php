<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Directives\Filters\InFilterDirective;
use App\GraphQL\Definition\Directives\Filters\NotInFilterDirective;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Schema\TypeRegistry;

/**
 * Class EnumField.
 */
abstract class EnumField extends Field implements FilterableField
{
    /**
     * Create a new field instance.
     *
     * @param  string  $column
     * @param  string  $enum
     * @param  string|null  $name
     * @param  bool  $nullable
     */
    public function __construct(
        public string $column,
        public string $enum,
        public ?string $name = null,
        public bool $nullable = true,
    ) {
        parent::__construct($column, $name, $nullable);
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    protected function type(): Type
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
     *
     * @param  mixed  $root
     * @return mixed
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
            new InFilterDirective($this, $this->type()),
            new NotInFilterDirective($this, $this->type()),
        ];
    }
}
