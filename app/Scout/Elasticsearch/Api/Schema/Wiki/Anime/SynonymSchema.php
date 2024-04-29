<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki\Anime;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Synonym\SynonymTextField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Synonym\SynonymTypeField;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\Anime\SynonymQuery;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\AnimeSchema;

/**
 * Class SynonymSchema.
 */
class SynonymSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @return ElasticQuery
     */
    public function query(): ElasticQuery
    {
        return new SynonymQuery();
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return SynonymResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), AnimeSynonym::RELATION_ANIME),
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
                new IdField($this, AnimeSynonym::ATTRIBUTE_ID),
                new SynonymTextField($this),
                new SynonymTypeField($this),
            ],
        );
    }
}
