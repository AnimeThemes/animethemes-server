<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki\Anime;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeSynonym\AnimeSynonymTextField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeSynonym\AnimeSynonymTypeField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\Models\Wiki\Anime\AnimeSynonym;

class AnimeSynonymType extends EloquentType
{
    public function description(): string
    {
        return "Represents an alternate title or common abbreviation for an anime.\n\nFor example, the anime Bakemonogatari has the anime synonym \"Monstory\".";
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(AnimeSynonym::ATTRIBUTE_ID, AnimeSynonym::class),
            new AnimeSynonymTextField(),
            new AnimeSynonymTypeField(),
            new LocalizedEnumField(new AnimeSynonymTypeField()),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),

            new BelongsToRelation(new AnimeType(), AnimeSynonym::RELATION_ANIME)
                ->nonNullable(),
        ];
    }
}
