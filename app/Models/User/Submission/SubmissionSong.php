<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_songs', 'id')]
class SubmissionSong extends Song
{
    use SubmissionModel;
}
