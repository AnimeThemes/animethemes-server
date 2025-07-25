<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use App\GraphQL\Attributes\Resolvers\UseAllDirective;
use App\GraphQL\Attributes\Resolvers\UseAuthDirective;
use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Attributes\Resolvers\UseFindDirective;
use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Attributes\UseSearchDirective;
use Illuminate\Support\Arr;
use ReflectionClass;

trait ResolvesAttributes
{
    /**
     * Resolve the all directive as an attribute.
     */
    protected function resolveAllAttribute(): bool
    {
        $reflection = new ReflectionClass($this);

        $attributes = $reflection->getAttributes(UseAllDirective::class);

        if (filled($attributes)) {
            $instance = Arr::first($attributes)->newInstance();

            return $instance->shouldUse;
        }

        return false;
    }

    /**
     * Resolve the auth directive as an attribute.
     */
    protected function resolveAuthAttribute(): bool
    {
        $reflection = new ReflectionClass($this);

        $attributes = $reflection->getAttributes(UseAuthDirective::class);

        if (filled($attributes)) {
            $instance = Arr::first($attributes)->newInstance();

            return $instance->shouldUse;
        }

        return false;
    }

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
     * Resolve the find directive as an attribute.
     */
    protected function resolveFindAttribute(): bool
    {
        $reflection = new ReflectionClass($this);

        $attributes = $reflection->getAttributes(UseFindDirective::class);

        if (filled($attributes)) {
            $instance = Arr::first($attributes)->newInstance();

            return $instance->shouldUse;
        }

        return false;
    }

    /**
     * Resolve the paginate directive as an attribute.
     */
    protected function resolvePaginateAttribute(): bool
    {
        $reflection = new ReflectionClass($this);

        $attributes = $reflection->getAttributes(UsePaginateDirective::class);

        if (filled($attributes)) {
            $instance = Arr::first($attributes)->newInstance();

            return $instance->shouldUse;
        }

        return false;
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
