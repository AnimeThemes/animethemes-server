<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class BaseEnum.
 */
abstract class BaseEnum extends Enum implements LocalizedEnum
{
    /**
     * Make a new instance from an enum description.
     *
     * @param  string  $description
     * @return static|null
     */
    public static function fromDescription(string $description): ?static
    {
        return Arr::first(
            static::getInstances(),
            fn (BaseEnum $enum) => Str::lower($enum->description) === Str::lower($description)
        );
    }

    /**
     * Attempt to instantiate a new Enum using the given key or value.
     * Note: library coerce function does not attempt unstrict value comparision.
     */
    public static function unstrictCoerce(mixed $enumKeyOrValue): ?static
    {
        return is_numeric($enumKeyOrValue)
            ? static::coerce(intval($enumKeyOrValue))
            : static::coerce($enumKeyOrValue);
    }
}
