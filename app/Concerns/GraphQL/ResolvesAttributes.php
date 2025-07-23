<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use App\GraphQL\Attributes\UseBuilder;
use App\GraphQL\Attributes\UseField;
use Exception;
use ReflectionClass;

/**
 * Trait ResolvesAttributes.
 */
trait ResolvesAttributes
{
    /**
     * Resolve the builder directive as an attribute.
     *
     * @return string|null
     */
    protected function resolveBuilderAttribute(): ?string
    {
        $reflection = new ReflectionClass($this);

        $attributes = $reflection->getAttributes(UseBuilder::class);

        if (count($attributes) === 1) {
            $instance = $attributes[0]->newInstance();

            return sprintf('%s@%s', $instance->builderClass, $instance->method);
        }

        if (count($attributes) > 1) {
            throw new Exception('Multiple builders disallowed.');
        }

        return null;
    }

    /**
     * Resolve the field directive as an attribute.
     *
     * @return string|null
     */
    protected function resolveFieldAttribute(): ?string
    {
        $reflection = new ReflectionClass($this);

        $attributes = $reflection->getAttributes(UseField::class);

        if (count($attributes) === 1) {
            $instance = $attributes[0]->newInstance();

            return sprintf('%s@%s', $instance->fieldClass, $instance->method);
        }

        if (count($attributes) > 1) {
            throw new Exception('Multiple fields disallowed.');
        }

        return null;
    }
}
