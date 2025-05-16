<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\Aggregate\ViewAggregate;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Interface HasAggregateViews.
 *
 * @property ViewAggregate|null $viewAggregate
 */
interface HasAggregateViews
{
    /**
     * Get the views count of the model.
     *
     * @return MorphOne
     */
    public function viewAggregate(): MorphOne;
}
