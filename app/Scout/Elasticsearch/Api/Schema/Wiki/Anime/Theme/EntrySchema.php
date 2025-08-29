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
use App\Scout\Elasticsearch\Api\Query\Wiki\Anime\Theme\EntryQuery;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\AnimeSchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\VideoSchema;
use Illuminate\Database\Eloquent\Model;

class EntrySchema extends Schema
{
    /**
     * The model this schema represents.
     */
    public function query(): EntryQuery
    {
        return new EntryQuery();
    }

    public function type(): string
    {
        return EntryResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), AnimeThemeEntry::RELATION_ANIME),
            new AllowedInclude(new ThemeSchema(), AnimeThemeEntry::RELATION_THEME),
            new AllowedInclude(new VideoSchema(), AnimeThemeEntry::RELATION_VIDEOS),
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
                new IdField($this, AnimeThemeEntry::ATTRIBUTE_ID),
                new EntryEpisodesField($this),
                new EntryNotesField($this),
                new EntryNsfwField($this),
                new EntrySpoilerField($this),
                new EntryVersionField($this),
            ],
        );
    }

    /**
     * Get the model of the schema.
     *
     * @return AnimeThemeEntry
     */
    public function model(): AnimeThemeEntry
    {
        return new AnimeThemeEntry();
    }
}
