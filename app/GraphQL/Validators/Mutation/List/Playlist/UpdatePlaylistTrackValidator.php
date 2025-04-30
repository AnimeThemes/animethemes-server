<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\Mutation\List\Playlist;

use App\Contracts\Models\HasHashids;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

/**
 * Class UpdatePlaylistTrackValidator.
 */
class UpdatePlaylistTrackValidator extends Validator
{
    /**
     * Specify validation rules for the arguments.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $playlistHashid = $this->arg('playlist');
        $hashid = $this->arg(PlaylistTrack::ATTRIBUTE_HASHID);
        $entryId = $this->arg(PlaylistTrack::ATTRIBUTE_ENTRY);
        $videoId = $this->arg(PlaylistTrack::ATTRIBUTE_VIDEO);

        $playlist = Playlist::query()->firstWhere(Playlist::ATTRIBUTE_HASHID, $playlistHashid);
        $track = PlaylistTrack::query()->firstWhere(PlaylistTrack::ATTRIBUTE_HASHID, $hashid);

        return [
            PlaylistTrack::ATTRIBUTE_ENTRY => [
                'sometimes',
                'required',
                'integer',
                Rule::exists(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_ID),
                Rule::when(
                    ! empty($videoId),
                    [
                        Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                            ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $videoId),
                    ]
                ),
            ],
            PlaylistTrack::RELATION_NEXT => [
                'sometimes',
                'required',
                'string',
                Str::of('prohibits:')->append(PlaylistTrack::RELATION_PREVIOUS)->__toString(),
                Rule::exists(PlaylistTrack::class, HasHashids::ATTRIBUTE_HASHID)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey())
                    ->whereNot(PlaylistTrack::ATTRIBUTE_ID, $track?->getKey()),
            ],
            PlaylistTrack::RELATION_PREVIOUS => [
                'sometimes',
                'required',
                'string',
                Str::of('prohibits:')->append(PlaylistTrack::RELATION_NEXT)->__toString(),
                Rule::exists(PlaylistTrack::class, HasHashids::ATTRIBUTE_HASHID)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey())
                    ->whereNot(PlaylistTrack::ATTRIBUTE_ID, $track?->getKey()),
            ],
            PlaylistTrack::ATTRIBUTE_VIDEO => [
                'sometimes',
                'required',
                'integer',
                Rule::exists(Video::class, Video::ATTRIBUTE_ID),
                Rule::when(
                    ! empty($entryId),
                    [
                        Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                            ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $entryId),
                    ]
                ),
            ],
        ];
    }
}
