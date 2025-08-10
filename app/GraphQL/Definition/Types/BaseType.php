<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Support\Relations\Relation;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Type as RebingType;

abstract class BaseType extends RebingType
{
    /**
     * Get the attributes of the type.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->description(),
        ];
    }

    /**
     * The name displayed of type.
     * By default, it will base on the class name without 'Type'.
     */
    public function getName(): string
    {
        return Str::of(class_basename($this))
            ->remove('Type')
            ->__toString();
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return '';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [];
    }

    /**
     * Convert the fields to rebing resolve.
     *
     * @return array<string, array<string, mixed>>
     */
    public function fields(): array
    {
        // TODO: Add relations
        return collect($this->fieldClasses())
            ->mapWithKeys(fn (Field $field) => [
                $field->getName() => [
                    'type' => $field->type(),
                    'description' => $field->description(),
                    'alias' => $field->getColumn(),
                    'resolve' => $field->resolve(...),
                ],
            ])
            ->toArray();
    }
}
