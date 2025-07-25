<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use App\GraphQL\Attributes\UseBuilder;
use App\GraphQL\Attributes\UseField;
use Illuminate\Support\Arr;
use ReflectionClass;

trait ResolvesAttributes
{
    /**
     * Resolve the builder directive as an attribute.
     */
    protected function resolveBuilderAttribute(): ?string
    {
        $reflection = new ReflectionClass($this);

        $attributes = [];

        while ($reflection) {
            $attributes = array_merge($attributes, $reflection->getAttributes(UseBuilder::class));
            $reflection = $reflection->getParentClass();
        }

        if (filled($attributes)) {
            $instance = Arr::first($attributes)->newInstance();

            return sprintf('%s@%s', $instance->builderClass, $instance->method);
        }

        return null;
    }

    /**
     * Resolve the field directive as an attribute.
     */
    protected function resolveFieldAttribute(): ?string
    {
        $reflection = new ReflectionClass($this);

        $attributes = [];

        while ($reflection) {
            $attributes = array_merge($attributes, $reflection->getAttributes(UseField::class));
            $reflection = $reflection->getParentClass();
        }

        if (filled($attributes)) {
            $instance = Arr::first($attributes)->newInstance();

            return sprintf('%s@%s', $instance->fieldClass, $instance->method);
        }

        return null;
    }
}
