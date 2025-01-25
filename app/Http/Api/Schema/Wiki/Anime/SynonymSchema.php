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
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SynonymSchema.
 */
class SynonymSchema extends EloquentSchema implements SearchableSchema
{
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
        return array_merge(
            $this->withIntermediatePaths([
                new AllowedInclude(new AnimeSchema(), AnimeSynonym::RELATION_ANIME),
            ]),
            []
        );
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
                new SynonymAnimeIdField($this),
            ],
        );
    }

    /**
     * Get the model of the schema.
     *
     * @return Model
     */
    public function model(): Model
    {
        return new AnimeSynonym();
    }
}
