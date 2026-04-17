<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_studios', 'id')]
class SubmissionStudio extends Studio
{
    use SubmissionModel;
}
