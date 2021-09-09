<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;
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
        foreach (static::getInstances() as $instance) {
            if (Str::lower($instance->description) === Str::lower($description)) {
                return $instance;
            }
        }

        return null;
    }
}
