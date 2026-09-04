<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Theme\ThemeAnimeIdField;
use App\Http\Api\Field\Wiki\Theme\ThemeGroupIdField;
use App\Http\Api\Field\Wiki\Theme\ThemeSequenceField;
use App\Http\Api\Field\Wiki\Theme\ThemeSlugField;
use App\Http\Api\Field\Wiki\Theme\ThemeSongIdField;
use App\Http\Api\Field\Wiki\Theme\ThemeTypeField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Resource\ThemeJsonResource;
use App\Models\Wiki\Theme;
use Illuminate\Database\Eloquent\Model;

class ThemeSchema extends EloquentSchema implements SearchableSchema
{
    final public const string SORT_SEASON = 'anime.season';

    final public const string SORT_TITLE = 'song.title';

    final public const string SORT_YEAR = 'anime.year';

    public function type(): string
    {
        return ThemeJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Theme::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), Theme::RELATION_ARTISTS),
            new AllowedInclude(new EntrySchema(), Theme::RELATION_ENTRIES),
            new AllowedInclude(new GroupSchema(), Theme::RELATION_GROUP),
            new AllowedInclude(new ImageSchema(), Theme::RELATION_IMAGES),
            new AllowedInclude(new SongSchema(), Theme::RELATION_SONG),
            new AllowedInclude(new VideoSchema(), Theme::RELATION_VIDEOS),

            // Undocumented paths needed for client builds
            new AllowedInclude(new AudioSchema(), 'animethemeentries.videos.audio'),
        ]);
    }

    /**
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField($this, Theme::ATTRIBUTE_ID),
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
     */
    public function model(): Model
    {
        return new Theme();
    }
}
