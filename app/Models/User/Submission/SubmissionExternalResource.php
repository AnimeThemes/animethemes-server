<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_resources', 'id')]
class SubmissionExternalResource extends ExternalResource
{
    use SubmissionModel;
}
