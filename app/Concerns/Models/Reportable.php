<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\User\Report\ReportStep;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait Reportable.
 */
trait Reportable
{
    /**
     * Get the reports made to the model.
     *
     * @return MorphMany
     */
    public function reportsteps(): MorphMany
    {
        return $this->morphMany(ReportStep::class, ReportStep::ATTRIBUTE_ACTIONABLE);
    }
}
