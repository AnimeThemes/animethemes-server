<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki\Anime;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeAnimeIdField;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeGroupIdField;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeSequenceField;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeSlugField;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeSongIdField;
use App\Http\Api\Field\Wiki\Anime\Theme\ThemeTypeField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Database\Eloquent\Model;

class ThemeSchema extends EloquentSchema implements SearchableSchema
{
    final public const SORT_SEASON = 'anime.season';

    final public const SORT_TITLE = 'song.title';

    final public const SORT_YEAR = 'anime.year';

    /**
     * Get the type of the resource.
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
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), AnimeTheme::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), AnimeTheme::RELATION_ARTISTS),
            new AllowedInclude(new EntrySchema(), AnimeTheme::RELATION_ENTRIES),
            new AllowedInclude(new GroupSchema(), AnimeTheme::RELATION_GROUP),
            new AllowedInclude(new ImageSchema(), AnimeTheme::RELATION_IMAGES),
            new AllowedInclude(new SongSchema(), AnimeTheme::RELATION_SONG),
            new AllowedInclude(new VideoSchema(), AnimeTheme::RELATION_VIDEOS),

            // Undocumented paths needed for client builds
            new AllowedInclude(new AudioSchema(), 'animethemeentries.videos.audio'),
        ]);
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
                new IdField($this, AnimeTheme::ATTRIBUTE_ID),
                new ThemeSequenceField($this),
                new ThemeSlugField($this),
                new ThemeTypeField($this),
                new ThemeAnimeIdField($this),
                new ThemeGroupIdField($this),
                new ThemeSongIdField($this),
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

    /**
     * Resolve the owner model of the schema.
     *
     * @return Model
     */
    public function model(): Model
    {
        return new AnimeTheme();
    }
}
