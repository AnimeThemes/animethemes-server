<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\Playlist\PlaylistTrack;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Resolvers\List\Playlist\PlaylistTrackResolver;
use App\GraphQL\Schema\Fields\Field;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class PlaylistTrackEntryIdField extends Field implements CreatableField, FilterableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::ATTRIBUTE_ENTRY, nullable: false);
    }

    public function description(): string
    {
        return 'The entry id of the track';
    }

    public function baseType(): Type
    {
        return Type::int();
    }

    public function getFilter(): IntFilter
    {
        return new IntFilter($this->getName(), $this->getColumn());
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            'integer',
            Rule::exists(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_ID),
            Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, Arr::get($args, PlaylistTrackResolver::ATTRIBUTE_VIDEO)),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        $videoId = Arr::get($args, PlaylistTrackResolver::ATTRIBUTE_VIDEO);

        return [
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
        ];
    }
}
