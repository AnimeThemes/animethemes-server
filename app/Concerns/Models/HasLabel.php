<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasLabel
{
    /**
     * Get the human-friendly label for the underlying model.
     */
    protected function privateLabel(Model $model): string
    {
        return Str::headline(class_basename($model));
    }
}
