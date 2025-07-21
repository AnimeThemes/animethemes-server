<?php

declare(strict_types=1);

namespace App\Filament\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Support\Uri;

/**
 * Class UriStateCast.
 */
class UriStateCast implements StateCast
{
    public function __construct(
        protected bool $isNullable = true,
    ) {}

    /**
     * @param  string|null  $state
     * @return Uri|null
     */
    public function get(mixed $state): ?Uri
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return Uri::of($state);
    }

    /**
     * @param  Uri|null  $state
     * @return string
     */
    public function set(mixed $state): ?string
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return $state->__toString();
    }
}
