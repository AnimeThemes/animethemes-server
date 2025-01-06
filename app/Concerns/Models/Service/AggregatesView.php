<?php

declare(strict_types=1);

namespace App\Concerns\Models\Service;

use App\Models\Service\ViewAggregate;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait AggregatesView.
 */
trait AggregatesView
{
    final public const RELATION_VIEW_AGGREGATE = 'viewAggregate';

    /**
     * Get the views count of the model.
     *
     * @return MorphOne<ViewAggregate, $this>
     */
    public function viewAggregate(): MorphOne
    {
        return $this->morphOne(
            ViewAggregate::class,
            ViewAggregate::ATTRIBUTE_VIEWABLE,
        );
    }
}
