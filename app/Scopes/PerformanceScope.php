<?php

declare(strict_types=1);

namespace App\Scopes;

use App\Models\Wiki\SongStaff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PerformanceScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where(SongStaff::ATTRIBUTE_ROLE, 'Performance');
    }
}
