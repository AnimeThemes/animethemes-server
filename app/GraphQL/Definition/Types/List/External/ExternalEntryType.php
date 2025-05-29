<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\List\External;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalEntry\ExternalEntryIsFavoriteField;
use App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalEntry\ExternalEntryScoreField;
use App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalEntry\ExternalEntryWatchStatusField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\List\ExternalProfileType;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\Models\List\External\ExternalEntry;

/**
 * Class ExternalEntryType.
 */
class ExternalEntryType extends EloquentType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents an anime entry on the external profile.\n\nFor example, Hibike Euphonium! is marked as completed on the profile AnimeThemes.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new ExternalProfileType(), ExternalEntry::RELATION_PROFILE, nullable: false),
            new BelongsToRelation(new AnimeType(), ExternalEntry::RELATION_ANIME, nullable: false),
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
            new IdField(ExternalEntry::ATTRIBUTE_ID),
            new ExternalEntryScoreField(),
            new ExternalEntryIsFavoriteField(),
            new ExternalEntryWatchStatusField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
