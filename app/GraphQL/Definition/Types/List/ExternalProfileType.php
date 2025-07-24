<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\List;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalProfileIdField;
use App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalProfileNameField;
use App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalProfileSiteField;
use App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalProfileVisibilityField;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\HasManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\List\External\ExternalEntryType;
use App\Models\List\ExternalProfile;

class ExternalProfileType extends EloquentType implements HasFields, HasRelations
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'Represents a user profile on the external site like MAL.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new HasManyRelation(new ExternalEntryType(), ExternalProfile::RELATION_EXTERNAL_ENTRIES),
            new BelongsToRelation(new UserType(), ExternalProfile::RELATION_USER),
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
            new ExternalProfileIdField(),
            new ExternalProfileNameField(),
            new ExternalProfileSiteField(),
            new LocalizedEnumField(new ExternalProfileSiteField()),
            new ExternalProfileVisibilityField(),
            new LocalizedEnumField(new ExternalProfileVisibilityField()),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
