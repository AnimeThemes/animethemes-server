<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\Playlist\PlaylistTrack;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\Field;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class PlaylistTrackVideoIdField extends Field implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::ATTRIBUTE_VIDEO, nullable: false);
    }

    public function description(): string
    {
        return 'The video id of the track';
    }

    public function baseType(): Type
    {
        return Type::int();
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            'integer',
            Rule::exists(Video::class, Video::ATTRIBUTE_ID),
            Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, Arr::get($args, PlaylistTrack::ATTRIBUTE_ENTRY)),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        $entryId = Arr::get($args, PlaylistTrack::ATTRIBUTE_ENTRY);

        return [
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
        ];
    }
}
