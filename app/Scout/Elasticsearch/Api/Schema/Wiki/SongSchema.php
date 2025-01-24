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
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\SongQuery;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\ThemeSchema;

/**
 * Class SongSchema.
 */
class SongSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @return ElasticQuery
     */
    public function query(): ElasticQuery
    {
        return new SongQuery();
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return SongResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    protected function finalAllowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), Song::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), Song::RELATION_ARTISTS),
            new AllowedInclude(new GroupSchema(), Song::RELATION_THEME_GROUPS),
            new AllowedInclude(new ThemeSchema(), Song::RELATION_ANIMETHEMES),
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
                new IdField($this, Song::ATTRIBUTE_ID),
                new SongTitleField($this),
            ],
        );
    }
}
