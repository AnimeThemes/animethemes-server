<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Models\Wiki\Synonym;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_synonyms', 'id')]
class SubmissionSynonym extends Synonym
{
    use SubmissionModel;
}
