<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Artist;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Artist\ArtistNameField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Artist\ArtistSlugField;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\ArtistQuery;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\ThemeSchema;

/**
 * Class ArtistSchema.
 */
class ArtistSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @return ElasticQuery
     */
    public function query(): ElasticQuery
    {
        return new ArtistQuery();
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ArtistResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), Artist::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), Artist::RELATION_GROUPS),
            new AllowedInclude(new ArtistSchema(), Artist::RELATION_MEMBERS),
            new AllowedInclude(new ExternalResourceSchema(), Artist::RELATION_RESOURCES),
            new AllowedInclude(new ImageSchema(), Artist::RELATION_IMAGES),
            new AllowedInclude(new SongSchema(), Artist::RELATION_SONGS),
            new AllowedInclude(new ThemeSchema(), Artist::RELATION_ANIMETHEMES),
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
                new IdField($this, Artist::ATTRIBUTE_ID),
                new ArtistNameField($this),
                new ArtistSlugField($this),
            ],
        );
    }
}
