<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Resources\Wiki\Resource\SeriesJsonResource;
use App\Models\Wiki\Series;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Series\SeriesNameField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Series\SeriesSlugField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class SeriesSchema extends Schema
{
    public function type(): string
    {
        return SeriesJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Series::RELATION_ANIME),
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
                new IdField($this, Series::ATTRIBUTE_ID),
                new SeriesNameField($this),
                new SeriesSlugField($this),
            ],
        );
    }
}
