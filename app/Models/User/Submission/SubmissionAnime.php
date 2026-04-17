<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Concerns\Models\SubmissionModel;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Support\Arr;

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
        Anime::ATTRIBUTE_NAME,
        Anime::ATTRIBUTE_SEASON,
        Anime::ATTRIBUTE_SLUG,
        Anime::ATTRIBUTE_SYNOPSIS,
        Anime::ATTRIBUTE_YEAR,
        'format',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return Arr::flatten([
            parent::casts(),
            'format' => AnimeMediaFormat::class,
        ]);
    }
}
