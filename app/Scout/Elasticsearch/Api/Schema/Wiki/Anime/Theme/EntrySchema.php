<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\Theme;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme\Entry\EntryEpisodesField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme\Entry\EntryNotesField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme\Entry\EntryNsfwField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme\Entry\EntrySpoilerField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme\Entry\EntryVersionField;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\Anime\Theme\EntryQuery;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\AnimeSchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\VideoSchema;

/**
 * Class EntrySchema.
 */
class EntrySchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @return ElasticQuery
     */
    public function query(): ElasticQuery
    {
        return new EntryQuery();
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return EntryResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    protected function finalAllowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), AnimeThemeEntry::RELATION_ANIME),
            new AllowedInclude(new ThemeSchema(), AnimeThemeEntry::RELATION_THEME),
            new AllowedInclude(new VideoSchema(), AnimeThemeEntry::RELATION_VIDEOS),
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
                new IdField($this, AnimeThemeEntry::ATTRIBUTE_ID),
                new EntryEpisodesField($this),
                new EntryNotesField($this),
                new EntryNsfwField($this),
                new EntrySpoilerField($this),
                new EntryVersionField($this),
            ],
        );
    }
}
