<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot;

use App\GraphQL\Definition\Types\EloquentType;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

abstract class PivotType extends EloquentType
{
    /**
     * Get the model string representation for the type.
     *
     * @return class-string<Pivot>
     */
    public function model(): string
    {
        return Str::of(class_basename($this))
            ->replace('GraphQL\\Definition\\Types\\Pivot', 'Pivots')
            ->remove('Type')
            ->__toString();
    }
}
