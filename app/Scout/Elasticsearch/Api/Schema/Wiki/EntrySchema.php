<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Resources\Wiki\Resource\EntryJsonResource;
use App\Models\Wiki\Entry;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Entry\EntryEpisodesField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Entry\EntryNotesField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Entry\EntryNsfwField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Entry\EntrySpoilerField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Entry\EntryVersionField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class EntrySchema extends Schema
{
    public function type(): string
    {
        return EntryJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Entry::RELATION_ANIME),
            new AllowedInclude(new ThemeSchema(), Entry::RELATION_THEME),
            new AllowedInclude(new VideoSchema(), Entry::RELATION_VIDEOS),
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
                new IdField($this, Entry::ATTRIBUTE_ID),
                new EntryEpisodesField($this),
                new EntryNotesField($this),
                new EntryNsfwField($this),
                new EntrySpoilerField($this),
                new EntryVersionField($this),
            ],
        );
    }

    public function model(): Entry
    {
        return new Entry();
    }
}
