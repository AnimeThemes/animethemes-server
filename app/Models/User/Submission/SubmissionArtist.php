<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Models\Wiki\Artist;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_artists', 'id')]
class SubmissionArtist extends Artist
{
    use SubmissionModel;
}
