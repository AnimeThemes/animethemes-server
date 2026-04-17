<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_anime_themes', 'id')]
class SubmissionAnimeTheme extends AnimeTheme
{
    use SubmissionModel;
}
