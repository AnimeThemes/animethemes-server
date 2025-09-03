<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\List;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\List\ExternalProfile\ExternalProfileIdField;
use App\GraphQL\Schema\Fields\List\ExternalProfile\ExternalProfileNameField;
use App\GraphQL\Schema\Fields\List\ExternalProfile\ExternalProfileSiteField;
use App\GraphQL\Schema\Fields\List\ExternalProfile\ExternalProfileVisibilityField;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Types\Auth\UserType;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\List\External\ExternalEntryType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\List\ExternalProfile;

class ExternalProfileType extends EloquentType
{
    public function description(): string
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
    public function fieldClasses(): array
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
