<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki\Anime;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Anime\Synonym\SynonymAnimeIdField;
use App\Http\Api\Field\Wiki\Anime\Synonym\SynonymTextField;
use App\Http\Api\Field\Wiki\Anime\Synonym\SynonymTypeField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\Wiki\Anime\Resource\SynonymJsonResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Model;

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
                new SynonymTextField($this),
                new SynonymTypeField($this),
                new SynonymAnimeIdField($this),
            ],
        );
    }

    /**
     * Get the model of the schema.
     */
    public function model(): AnimeSynonym
    {
        return new AnimeSynonym();
    }
}
