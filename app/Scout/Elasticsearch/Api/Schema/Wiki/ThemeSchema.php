<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Resource\ThemeJsonResource;
use App\Models\Wiki\Theme;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Theme\ThemeSequenceField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Theme\ThemeSlugField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Theme\ThemeTypeField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class ThemeSchema extends Schema
{
    final public const string SORT_SEASON = 'anime.season';

    final public const string SORT_TITLE = 'song.title';

    final public const string SORT_TITLE_FIELD = 'song.title_keyword';

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
            new AllowedInclude(new ImageSchema(), Theme::RELATION_IMAGES),
            new AllowedInclude(new SongSchema(), Theme::RELATION_SONG),
            new AllowedInclude(new VideoSchema(), Theme::RELATION_VIDEOS),
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
                new Sort(ThemeSchema::SORT_TITLE, ThemeSchema::SORT_TITLE_FIELD),
                new Sort(ThemeSchema::SORT_YEAR),
            ]
        );
    }

    public function model(): Theme
    {
        return new Theme();
    }
}
