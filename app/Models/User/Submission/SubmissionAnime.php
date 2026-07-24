<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('submission_anime', 'id')]
class SubmissionAnime extends Anime
{
    use SubmissionModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Anime::ATTRIBUTE_FORMAT,
        Anime::ATTRIBUTE_SEASON,
        Anime::ATTRIBUTE_SLUG,
        Anime::ATTRIBUTE_SYNOPSIS,
        Anime::ATTRIBUTE_TITLE,
        Anime::ATTRIBUTE_TITLE_ENGLISH,
        Anime::ATTRIBUTE_TITLE_NATIVE,
        Anime::ATTRIBUTE_YEAR,
    ];
}
