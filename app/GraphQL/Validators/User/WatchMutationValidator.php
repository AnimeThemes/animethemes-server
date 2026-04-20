<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\User;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class WatchMutationValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'entryId' => [
                'required',
                'integer',
                Rule::exists(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_ID),
                Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                    ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $this->arg('videoId')),
            ],
            'videoId' => [
                'required',
                'integer',
                Rule::exists(Video::class, Video::ATTRIBUTE_ID),
                Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                    ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $this->arg('entryId')),
            ],
        ];
    }
}
