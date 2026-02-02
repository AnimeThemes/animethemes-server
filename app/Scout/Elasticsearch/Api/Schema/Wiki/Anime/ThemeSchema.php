<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki\Anime;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Anime\Resource\ThemeJsonResource;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme\ThemeSequenceField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme\ThemeSlugField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme\ThemeTypeField;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\AnimeSchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\ArtistSchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\SongSchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\VideoSchema;

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
            new AllowedInclude(new AnimeSchema(), AnimeTheme::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), AnimeTheme::RELATION_ARTISTS),
            new AllowedInclude(new EntrySchema(), AnimeTheme::RELATION_ENTRIES),
            new AllowedInclude(new ImageSchema(), AnimeTheme::RELATION_IMAGES),
            new AllowedInclude(new SongSchema(), AnimeTheme::RELATION_SONG),
            new AllowedInclude(new VideoSchema(), AnimeTheme::RELATION_VIDEOS),
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
                new IdField($this, AnimeTheme::ATTRIBUTE_ID),
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

    public function model(): AnimeTheme
    {
        return new AnimeTheme();
    }
}
