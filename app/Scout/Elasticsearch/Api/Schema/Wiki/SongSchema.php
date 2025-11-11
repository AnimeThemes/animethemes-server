<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Song\SongTitleField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Song\SongTitleNativeField;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\ThemeSchema;

class SongSchema extends Schema
{
    public function type(): string
    {
        return SongResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Song::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), Song::RELATION_ARTISTS),
            new AllowedInclude(new GroupSchema(), Song::RELATION_THEME_GROUPS),
            new AllowedInclude(new ThemeSchema(), Song::RELATION_ANIMETHEMES),
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
                new IdField($this, Song::ATTRIBUTE_ID),
                new SongTitleField($this),
                new SongTitleNativeField($this),
            ],
        );
    }
}
