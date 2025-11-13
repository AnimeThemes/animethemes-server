<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\AnimeMediaFormatField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\AnimeNameField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\AnimeSeasonField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\AnimeSlugField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\AnimeSynopsisField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\AnimeYearField;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\ThemeSchema;

class AnimeSchema extends Schema
{
    public function type(): string
    {
        return AnimeResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new ArtistSchema(), Anime::RELATION_ARTISTS),
            new AllowedInclude(new AudioSchema(), Anime::RELATION_AUDIO),
            new AllowedInclude(new EntrySchema(), Anime::RELATION_ENTRIES),
            new AllowedInclude(new ExternalResourceSchema(), Anime::RELATION_RESOURCES),
            new AllowedInclude(new GroupSchema(), Anime::RELATION_GROUPS),
            new AllowedInclude(new ImageSchema(), Anime::RELATION_IMAGES),
            new AllowedInclude(new ScriptSchema(), Anime::RELATION_SCRIPTS),
            new AllowedInclude(new SeriesSchema(), Anime::RELATION_SERIES),
            new AllowedInclude(new SongSchema(), Anime::RELATION_SONG),
            new AllowedInclude(new StudioSchema(), Anime::RELATION_STUDIOS),
            new AllowedInclude(new SynonymSchema(), Anime::RELATION_SYNONYMS),
            new AllowedInclude(new ThemeSchema(), Anime::RELATION_THEMES),
            new AllowedInclude(new VideoSchema(), Anime::RELATION_VIDEOS),
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
                new IdField($this, Anime::ATTRIBUTE_ID),
                new AnimeNameField($this),
                new AnimeMediaFormatField($this),
                new AnimeSeasonField($this),
                new AnimeSlugField($this),
                new AnimeSynopsisField($this),
                new AnimeYearField($this),
            ],
        );
    }
}
