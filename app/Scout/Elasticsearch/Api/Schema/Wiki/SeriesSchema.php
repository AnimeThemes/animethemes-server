<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Series;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Series\SeriesNameField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Series\SeriesSlugField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class SeriesSchema.
 */
class SeriesSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return Series::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return SeriesResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), Series::RELATION_ANIME),
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
                new IdField($this, Series::ATTRIBUTE_ID),
                new SeriesNameField($this),
                new SeriesSlugField($this),
            ],
        );
    }
}
