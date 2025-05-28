<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\List;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalProfileNameField;
use App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalProfileSiteField;
use App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalProfileVisibilityField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\HasManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\List\External\ExternalEntryType;
use App\Models\List\ExternalProfile;

/**
 * Class ExternalProfileType.
 */
class ExternalProfileType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a user profile on the external site like MAL.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
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
     * @return array
     */
    public function fields(): array
    {
        return [
            new IdField(ExternalProfile::ATTRIBUTE_ID),
            new ExternalProfileNameField(),
            new ExternalProfileSiteField(),
            new ExternalProfileVisibilityField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
