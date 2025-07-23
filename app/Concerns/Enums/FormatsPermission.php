<?php

declare(strict_types=1);

namespace App\Concerns\Enums;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait FormatsPermission
{
    /**
     * Format permission name for model.
     *
     * @param  class-string<Model>  $modelClass
     */
    public function format(string $modelClass): string
    {
        return Str::of($this->value)
            ->append(class_basename($modelClass))
            ->snake(' ')
            ->__toString();
    }
}
