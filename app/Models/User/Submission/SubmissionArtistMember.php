<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_artist_member', 'id')]
class SubmissionArtistMember extends ArtistMember
{
    use SubmissionModel;
}
