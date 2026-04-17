<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Models\Wiki\Song\Performance;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_performances', 'id')]
class SubmissionPerformance extends Performance
{
    use SubmissionModel;
}
