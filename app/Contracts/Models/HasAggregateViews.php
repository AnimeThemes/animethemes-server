<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\Aggregate\ViewAggregate;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property ViewAggregate|null $viewAggregate
 */
interface HasAggregateViews
{
    /**
     * @return MorphOne
     */
    public function viewAggregate(): MorphOne;
}
