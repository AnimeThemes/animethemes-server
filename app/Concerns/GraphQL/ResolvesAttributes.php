<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use App\GraphQL\Attributes\Deprecated;
use Illuminate\Support\Arr;
use ReflectionClass;

trait ResolvesAttributes
{
    /**
     * Resolve the deprecated directive as an attribute.
     */
    protected function resolveDeprecatedAttribute(): ?string
    {
        $reflection = new ReflectionClass($this);

        $attributes = $reflection->getAttributes(Deprecated::class);

        if (filled($attributes)) {
            $instance = Arr::first($attributes)->newInstance();

            return $instance->reason;
        }

        return null;
    }
}
