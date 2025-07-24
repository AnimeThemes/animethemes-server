<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\Field;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class PlaylistTrackVideoIdField extends Field implements CreatableField, RequiredOnCreation, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(PlaylistTrack::ATTRIBUTE_VIDEO, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The video id of the track';
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
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
            Rule::exists(Video::class, Video::ATTRIBUTE_ID),
            Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, Arr::get($args, PlaylistTrack::ATTRIBUTE_ENTRY)),
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
        $entryId = Arr::get($args, PlaylistTrack::ATTRIBUTE_ENTRY);

        return [
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
        ];
    }
}
