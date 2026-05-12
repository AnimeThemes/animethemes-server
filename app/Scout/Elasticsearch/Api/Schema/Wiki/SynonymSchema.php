<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Resources\Wiki\Resource\SynonymJsonResource;
use App\Models\Wiki\Synonym;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Synonym\SynonymTextField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Synonym\SynonymTypeField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class SynonymSchema extends Schema
{
    public function type(): string
    {
        return SynonymJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), 'anime'),
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
                new IdField($this, Synonym::ATTRIBUTE_ID),
                new SynonymTextField($this),
                new SynonymTypeField($this),
            ],
        );
    }
}
