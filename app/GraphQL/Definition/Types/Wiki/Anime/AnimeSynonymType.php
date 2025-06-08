<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Anime;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSynonym\AnimeSynonymTextField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSynonym\AnimeSynonymTypeField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\Models\Wiki\Anime\AnimeSynonym;

/**
 * Class AnimeSynonymType.
 */
class AnimeSynonymType extends EloquentType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents an alternate title or common abbreviation for an anime.\n\nFor example, the anime Bakemonogatari has the anime synonym \"Monstory\".";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeType(), AnimeSynonym::RELATION_ANIME, nullable: false),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new IdField(AnimeSynonym::ATTRIBUTE_ID),
            new AnimeSynonymTextField(),
            new AnimeSynonymTypeField(),
            new LocalizedEnumField(new AnimeSynonymTypeField()),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
