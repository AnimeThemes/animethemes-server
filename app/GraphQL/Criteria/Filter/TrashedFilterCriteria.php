<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Filter;

use App\Enums\GraphQL\Filter\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;

class TrashedFilterCriteria extends FilterCriteria
{
    /**
     * Apply the filtering to the current Eloquent builder.
     */
    public function filter(Builder $builder): Builder
    {
        return match ($this->value) {
            /** @phpstan-ignore-next-line */
            TrashedFilter::WITH => $builder->withTrashed(),
            /** @phpstan-ignore-next-line */
            TrashedFilter::WITHOUT => $builder->withoutTrashed(),
            /** @phpstan-ignore-next-line */
            TrashedFilter::ONLY => $builder->onlyTrashed(),
            default => $builder,
        };
    }
}
