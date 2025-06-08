<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use Illuminate\Support\Arr;

/**
 * Class LocalizedEnumField.
 */
class LocalizedEnumField extends StringField
{
    /**
     * Create a new field instance.
     *
     * @param  EnumField  $field
     */
    public function __construct(
        protected EnumField $field,
    ) {
        parent::__construct($field->column, $field->getName().'Localized', $field->nullable);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return "The formatted string value of the {$this->field->getName()} field";
    }

    /**
     * Get the directives of the field.
     *
     * @return array
     */
    public function directives(): array
    {
        return [
            'enumField' => [
                'localize' => true,
            ],
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
