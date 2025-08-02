<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Anime;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSynonym\AnimeSynonymTextField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSynonym\AnimeSynonymTypeField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Anime\AnimeSynonym;

class AnimeSynonymType extends EloquentType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return "Represents an alternate title or common abbreviation for an anime.\n\nFor example, the anime Bakemonogatari has the anime synonym \"Monstory\".";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeType(), AnimeSynonym::RELATION_ANIME)
                ->notNullable(),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new IdField(AnimeSynonym::ATTRIBUTE_ID, AnimeSynonym::class),
            new AnimeSynonymTextField(),
            new AnimeSynonymTypeField(),
            new LocalizedEnumField(new AnimeSynonymTypeField()),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
