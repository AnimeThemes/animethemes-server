<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\Field;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class PlaylistTrackEntryIdField extends Field implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::ATTRIBUTE_ENTRY, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The entry id of the track';
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::int();
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            'integer',
            Rule::exists(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_ID),
            Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, Arr::get($args, PlaylistTrack::ATTRIBUTE_VIDEO)),
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        $videoId = Arr::get($args, PlaylistTrack::ATTRIBUTE_VIDEO);

        return [
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
        ];
    }
}
