<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Fields\Relations\MorphToRelation;
use App\GraphQL\Schema\Fields\Wiki\Synonym\SynonymTextField;
use App\GraphQL\Schema\Fields\Wiki\Synonym\SynonymTypeField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Unions\SynonymableUnion;
use App\Models\Wiki\Synonym;

class SynonymType extends EloquentType
{
    public function description(): string
    {
        return "Represents an alternate title or common abbreviation for an entity.\n\nFor example, the anime Bakemonogatari has the synonym \"Monstory\".";
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(Synonym::ATTRIBUTE_ID, Synonym::class),
            new SynonymTextField(),
            new SynonymTypeField(),
            new LocalizedEnumField(new SynonymTypeField()),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),

            new MorphToRelation(new SynonymableUnion(), Synonym::RELATION_SYNONYMABLE)
                ->nonNullable(),
        ];
    }
}
