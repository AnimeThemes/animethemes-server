<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\List\External;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\List\ExternalProfile\ExternalEntry\ExternalEntryIsFavoriteField;
use App\GraphQL\Schema\Fields\List\ExternalProfile\ExternalEntry\ExternalEntryScoreField;
use App\GraphQL\Schema\Fields\List\ExternalProfile\ExternalEntry\ExternalEntryWatchStatusField;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\List\ExternalProfileType;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\Models\List\External\ExternalEntry;

class ExternalEntryType extends EloquentType
{
    public function description(): string
    {
        return "Represents an anime entry on the external profile.\n\nFor example, Hibike Euphonium! is marked as completed on the profile AnimeThemes.";
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(ExternalEntry::ATTRIBUTE_ID, ExternalEntry::class),
            new ExternalEntryScoreField(),
            new ExternalEntryIsFavoriteField(),
            new ExternalEntryWatchStatusField(),
            new LocalizedEnumField(new ExternalEntryWatchStatusField()),
            new CreatedAtField(),
            new UpdatedAtField(),

            new BelongsToRelation(new ExternalProfileType(), ExternalEntry::RELATION_PROFILE)
                ->nonNullable(),
            new BelongsToRelation(new AnimeType(), ExternalEntry::RELATION_ANIME)
                ->nonNullable(),
        ];
    }
}
