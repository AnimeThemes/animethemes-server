<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use App\GraphQL\Attributes\UseBuilderDirective;
use App\GraphQL\Attributes\UseFieldDirective;
use App\GraphQL\Attributes\UseSearchDirective;
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
            $attributes = array_merge($attributes, $reflection->getAttributes(UseBuilderDirective::class));
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
            $attributes = array_merge($attributes, $reflection->getAttributes(UseFieldDirective::class));
            $reflection = $reflection->getParentClass();
        }

        if (filled($attributes)) {
            $instance = Arr::first($attributes)->newInstance();

            return sprintf('%s@%s', $instance->fieldClass, $instance->method);
        }

        return null;
    }

    /**
     * Resolve the search directive as an attribute.
     */
    protected function resolveSearchAttribute(): bool
    {
        $reflection = new ReflectionClass($this);

        $attributes = [];

        while ($reflection) {
            $attributes = array_merge($attributes, $reflection->getAttributes(UseSearchDirective::class));
            $reflection = $reflection->getParentClass();
        }

        return filled($attributes);
    }
}
