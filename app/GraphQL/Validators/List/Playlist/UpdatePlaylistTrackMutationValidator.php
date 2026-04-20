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

class UpdatePlaylistTrackMutationValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        $playlist = Playlist::query()->firstWhere(Playlist::ATTRIBUTE_HASHID, $this->arg('playlist'));

        $track = PlaylistTrack::query()->firstWhere(PlaylistTrack::ATTRIBUTE_HASHID, $this->arg('id'));

        $entryId = $this->arg('entryId');
        $videoId = $this->arg('videoId');

        return [
            'entryId' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_ID),
                Rule::when(
                    filled($videoId),
                    [
                        Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                            ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $videoId),
                    ]
                ),
            ],
            'videoId' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists(Video::class, Video::ATTRIBUTE_ID),
                Rule::when(
                    filled($entryId),
                    [
                        Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                            ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $entryId),
                    ]
                ),
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
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey())
                    ->whereNot(PlaylistTrack::ATTRIBUTE_ID, $track?->getKey()),
            ],
            'next' => [
                'sometimes',
                'required',
                'string',
                Str::of('prohibits:')->append('previous')->__toString(),
                Rule::exists(PlaylistTrack::class, HasHashids::ATTRIBUTE_HASHID)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey())
                    ->whereNot(PlaylistTrack::ATTRIBUTE_ID, $track?->getKey()),
            ],
        ];
    }
}
