<?php

declare(strict_types=1);

namespace App\Concerns\Enums;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Trait CoercesInstances.
 */
trait CoercesInstances
{
    /**
     * Attempt to instantiate a new enum using the given name or value.
     */
    public static function unstrictCoerce(mixed $enumKeyOrValue): ?static
    {
        return is_numeric($enumKeyOrValue)
            ? static::coerce(intval($enumKeyOrValue))
            : static::coerce(Str::lower($enumKeyOrValue));
    }

    /**
     * Attempt to instantiate a new enum using the given name or value.
     */
    private static function coerce(mixed $enumNameOrValue): ?static
    {
        if ($enumNameOrValue === null) {
            return null;
        }

        if ($enumNameOrValue instanceof static) {
            return $enumNameOrValue;
        }

        $enum = Arr::first(
            static::cases(),
            fn (self $enum) => $enum->value === $enumNameOrValue
        );

        if (is_string($enumNameOrValue) && $enum === null) {
            $enum = Arr::first(
                static::cases(),
                fn (self $enum) => Str::lower($enum->name) === $enumNameOrValue
            );
        }

        return $enum;
    }
}
