<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\BaseModel;
use Illuminate\Support\Str;

trait HasLabel
{
    /**
     * Get the human-friendly label for the underlying model.
     */
    protected function privateLabel(BaseModel $model): string
    {
        return Str::headline(class_basename($model));
    }
}
