<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Schema\TypeRegistry;

/**
 * Class EnumField.
 */
abstract class EnumField extends Field
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
        protected string $column,
        protected string $enum,
        protected ?string $name = null,
        protected bool $nullable = true,
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
            'localizedEnum' => [],
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
        return Arr::get($root, $this->column)?->localize();
    }
}
