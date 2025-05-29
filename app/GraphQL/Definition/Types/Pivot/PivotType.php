<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot;

use App\GraphQL\Definition\Types\EloquentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class PivotType.
 */
abstract class PivotType extends EloquentType
{
    /**
     * Get the model string representation for the type.
     *
     * @return class-string<Model>
     */
    public function model(): string
    {
        return Str::of(class_basename($this))
            ->replace('GraphQL\\Definition\\Types', 'Pivots')
            ->__toString();
    }
}
