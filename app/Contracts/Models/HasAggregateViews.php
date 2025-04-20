<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\Service\ViewAggregate;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * interface HasAggregateViews.
 */
interface HasAggregateViews
{
    /**
     * Get the views count of the model.
     *
     * @return MorphOne<ViewAggregate, $this>
     */
    public function viewAggregate(): MorphOne;
}
