<?php

declare(strict_types=1);

namespace App\GraphQL\Validators\Mutation\List\Playlist\Track;

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
 * Class CreatePlaylistTrackValidator.
 */
class CreatePlaylistTrackValidator extends Validator
{
    /**
     * Specify validation rules for the arguments.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $playlistHashid = $this->arg('playlist');
        $playlist = Playlist::query()->firstWhere(Playlist::ATTRIBUTE_HASHID, $playlistHashid);

        return [
            PlaylistTrack::ATTRIBUTE_ENTRY => [
                'required',
                'integer',
                Rule::exists(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_ID),
                Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                    ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $this->arg(PlaylistTrack::ATTRIBUTE_VIDEO)),
            ],
            PlaylistTrack::RELATION_NEXT => [
                'sometimes',
                'required',
                'string',
                Str::of('prohibits:')->append(PlaylistTrack::RELATION_PREVIOUS)->__toString(),
                Rule::exists(PlaylistTrack::class, HasHashids::ATTRIBUTE_HASHID)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey()),
            ],
            PlaylistTrack::RELATION_PREVIOUS => [
                'sometimes',
                'required',
                'string',
                Str::of('prohibits:')->append(PlaylistTrack::RELATION_NEXT)->__toString(),
                Rule::exists(PlaylistTrack::class, HasHashids::ATTRIBUTE_HASHID)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey()),
            ],
            PlaylistTrack::ATTRIBUTE_VIDEO => [
                'required',
                'integer',
                Rule::exists(Video::class, Video::ATTRIBUTE_ID),
                Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                    ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $this->arg(PlaylistTrack::ATTRIBUTE_ENTRY)),
            ],
        ];
    }
}
