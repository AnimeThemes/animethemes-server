<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionComparison;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait SubmissionModel
{
    public function submission(): MorphToMany
    {
        return $this->morphToMany(
            Submission::class,
            SubmissionComparison::RELATION_SUBMITTED,
            SubmissionComparison::TABLE,
            SubmissionComparison::ATTRIBUTE_SUBMITTED_TYPE,
            SubmissionComparison::ATTRIBUTE_SUBMISSION
        );
    }
}
