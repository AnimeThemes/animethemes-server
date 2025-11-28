<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\User\Submission\SubmissionStep;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Submitable
{
    public function submissionsteps(): MorphMany
    {
        return $this->morphMany(SubmissionStep::class, SubmissionStep::RELATION_ACTIONABLE);
    }
}
