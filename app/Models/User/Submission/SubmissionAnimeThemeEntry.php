<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_anime_theme_entries', 'id')]
class SubmissionAnimeThemeEntry extends AnimeThemeEntry
{
    use SubmissionModel;
}
