<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki\Anime;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Anime\Synonym\AnimeSynonymAnimeIdField;
use App\Http\Api\Field\Wiki\Anime\Synonym\AnimeSynonymTextField;
use App\Http\Api\Field\Wiki\Anime\Synonym\AnimeSynonymTypeField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\Wiki\Anime\Resource\AnimeSynonymJsonResource;
use App\Models\Wiki\Anime\AnimeSynonym;

class AnimeSynonymSchema extends EloquentSchema implements SearchableSchema
{
    public function type(): string
    {
        return AnimeSynonymJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), AnimeSynonym::RELATION_ANIME),
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
                new IdField($this, AnimeSynonym::ATTRIBUTE_ID),
                new AnimeSynonymTextField($this),
                new AnimeSynonymTypeField($this),
                new AnimeSynonymAnimeIdField($this),
            ],
        );
    }
}
