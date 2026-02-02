<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\Wiki\Resource\ArtistJsonResource;
use App\Models\Wiki\Artist;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Artist\ArtistInformationField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Artist\ArtistNameField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Artist\ArtistSlugField;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\ThemeSchema;

class ArtistSchema extends Schema
{
    public function type(): string
    {
        return ArtistJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Artist::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), Artist::RELATION_GROUPS),
            new AllowedInclude(new ArtistSchema(), Artist::RELATION_MEMBERS),
            new AllowedInclude(new ExternalResourceSchema(), Artist::RELATION_RESOURCES),
            new AllowedInclude(new GroupSchema(), Artist::RELATION_THEME_GROUPS),
            new AllowedInclude(new ImageSchema(), Artist::RELATION_IMAGES),
            new AllowedInclude(new SongSchema(), Artist::RELATION_SONGS),
            new AllowedInclude(new ThemeSchema(), Artist::RELATION_ANIMETHEMES),
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
                new IdField($this, Artist::ATTRIBUTE_ID),
                new ArtistNameField($this),
                new ArtistSlugField($this),
                new ArtistInformationField($this),
            ],
        );
    }
}
