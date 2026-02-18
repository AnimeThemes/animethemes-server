<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Synonym\SynonymSynonymableIdField;
use App\Http\Api\Field\Wiki\Synonym\SynonymSynonymableTypeField;
use App\Http\Api\Field\Wiki\Synonym\SynonymTextField;
use App\Http\Api\Field\Wiki\Synonym\SynonymTypeField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Wiki\Resource\SynonymJsonResource;
use App\Models\Wiki\Synonym;

class SynonymSchema extends EloquentSchema implements SearchableSchema
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
        return $this->withIntermediatePaths();
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
                new SynonymSynonymableTypeField($this),
                new SynonymSynonymableIdField($this),
            ],
        );
    }
}
