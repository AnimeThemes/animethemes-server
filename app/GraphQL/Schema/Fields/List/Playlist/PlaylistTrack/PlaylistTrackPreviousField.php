<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\Playlist\PlaylistTrack;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Contracts\Models\HasHashids;
use App\GraphQL\Schema\Fields\Field;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlaylistTrackPreviousField extends Field implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::RELATION_PREVIOUS, nullable: true);
    }

    public function description(): string
    {
        return 'The previous track of the current track';
    }

    public function baseType(): Type
    {
        return Type::string();
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        $playlistHashid = Arr::get($args, 'playlist');
        $playlist = Playlist::query()->firstWhere(Playlist::ATTRIBUTE_HASHID, $playlistHashid);

        return [
            'sometimes',
            'required',
            'string',
            Str::of('prohibits:')->append(PlaylistTrack::RELATION_NEXT)->__toString(),
            Rule::exists(PlaylistTrack::class, HasHashids::ATTRIBUTE_HASHID)
                ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey()),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        $playlistHashid = Arr::get($args, 'playlist');
        $hashid = Arr::get($args, PlaylistTrack::ATTRIBUTE_HASHID);

        $playlist = Playlist::query()->firstWhere(Playlist::ATTRIBUTE_HASHID, $playlistHashid);
        $track = PlaylistTrack::query()->firstWhere(PlaylistTrack::ATTRIBUTE_HASHID, $hashid);

        return [
            'sometimes',
            'required',
            'string',
            Str::of('prohibits:')->append(PlaylistTrack::RELATION_NEXT)->__toString(),
            Rule::exists(PlaylistTrack::class, HasHashids::ATTRIBUTE_HASHID)
                ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey())
                ->whereNot(PlaylistTrack::ATTRIBUTE_ID, $track?->getKey()),
        ];
    }
}
