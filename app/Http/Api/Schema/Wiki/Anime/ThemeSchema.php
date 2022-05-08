<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki\Anime;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeAnimeIdField;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeGroupField;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeSequenceField;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeSlugField;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeSongIdField;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeTypeField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime\AnimeTheme;

/**
 * Class ThemeSchema.
 */
class ThemeSchema extends EloquentSchema
{
    final public const SORT_SEASON = 'anime.season';

    final public const SORT_TITLE = 'song.title';

    final public const SORT_YEAR = 'anime.year';

    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return AnimeTheme::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ThemeResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(AnimeSchema::class, AnimeTheme::RELATION_ANIME),
            new AllowedInclude(ArtistSchema::class, AnimeTheme::RELATION_ARTISTS),
            new AllowedInclude(EntrySchema::class, AnimeTheme::RELATION_ENTRIES),
            new AllowedInclude(ImageSchema::class, AnimeTheme::RELATION_IMAGES),
            new AllowedInclude(SongSchema::class, AnimeTheme::RELATION_SONG),
            new AllowedInclude(VideoSchema::class, AnimeTheme::RELATION_VIDEOS),
        ];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField(AnimeTheme::ATTRIBUTE_ID),
                new ThemeGroupField(),
                new ThemeSequenceField(),
                new ThemeSlugField(),
                new ThemeTypeField(),
                new ThemeAnimeIdField(),
                new ThemeSongIdField(),
            ],
        );
    }

    /**
     * Get the sorts of the resource.
     *
     * @return Sort[]
     */
    public function sorts(): array
    {
        return array_merge(
            parent::sorts(),
            [
                new Sort(ThemeSchema::SORT_SEASON),
                new Sort(ThemeSchema::SORT_TITLE),
                new Sort(ThemeSchema::SORT_YEAR),
            ]
        );
    }
}
