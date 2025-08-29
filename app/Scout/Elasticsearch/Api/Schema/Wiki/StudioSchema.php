<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\Wiki\Studio;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Studio\StudioNameField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Studio\StudioSlugField;
use App\Scout\Elasticsearch\Api\Query\Wiki\StudioQuery;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class StudioSchema extends Schema
{
    /**
     * The model this schema represents.
     */
    public function query(): StudioQuery
    {
        return new StudioQuery();
    }

    public function type(): string
    {
        return StudioResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Studio::RELATION_ANIME),
            new AllowedInclude(new ExternalResourceSchema(), Studio::RELATION_RESOURCES),
            new AllowedInclude(new ImageSchema(), Studio::RELATION_IMAGES),
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
                new IdField($this, Studio::ATTRIBUTE_ID),
                new StudioNameField($this),
                new StudioSlugField($this),
            ],
        );
    }
}
