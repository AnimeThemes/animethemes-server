<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\List\Playlist;

use App\Contracts\Models\HasHashids;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class CreatePlaylistTrackMutationValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        $playlist = Playlist::query()->firstWhere(Playlist::ATTRIBUTE_HASHID, $this->arg('playlist'));

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
            'position' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ],
            'previous' => [
                'sometimes',
                'required',
                'string',
                Str::of('prohibits:')->append('next')->__toString(),
                Rule::exists(PlaylistTrack::class, HasHashids::ATTRIBUTE_HASHID)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey()),
            ],
            'next' => [
                'sometimes',
                'required',
                'string',
                Str::of('prohibits:')->append('previous')->__toString(),
                Rule::exists(PlaylistTrack::class, HasHashids::ATTRIBUTE_HASHID)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey()),
            ],
        ];
    }
}
