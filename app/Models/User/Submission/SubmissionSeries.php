<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Models\Wiki\Series;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_series', 'id')]
class SubmissionSeries extends Series
{
    use SubmissionModel;
}
