<?php

declare(strict_types=1);

namespace App\Concerns\Enums;

use Illuminate\Support\Str;

/**
 * Trait FormatsPermission.
 */
trait FormatsPermission
{
    /**
     * Format permission name for model.
     *
     * @param  string  $modelClass
     * @return string
     */
    public function format(string $modelClass): string
    {
        return Str::of($this->value)
            ->append(class_basename($modelClass))
            ->snake(' ')
            ->__toString();
    }
}
