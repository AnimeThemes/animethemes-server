<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\User\Report\ReportStep;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Reportable
{
    public function reportsteps(): MorphMany
    {
        return $this->morphMany(ReportStep::class, ReportStep::RELATION_ACTIONABLE);
    }
}
