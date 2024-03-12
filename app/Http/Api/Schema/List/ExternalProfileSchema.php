<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\List;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\List\ExternalProfile\ExternalProfileUsernameField;
use App\Http\Api\Field\List\ExternalProfile\ExternalProfileIdField;
use App\Http\Api\Field\List\ExternalProfile\ExternalProfileVisibilityField;
use App\Http\Api\Field\List\ExternalProfile\ExternalProfileSiteField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Auth\UserSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\External\ExternalEntrySchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\List\Resource\ExternalProfileResource;
use App\Models\List\ExternalProfile;

/**
 * Class ExternalProfileSchema.
 */
class ExternalProfileSchema extends EloquentSchema implements SearchableSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ExternalProfileResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), ExternalProfile::RELATION_ANIMES),
            new AllowedInclude(new ExternalEntrySchema(), ExternalProfile::RELATION_EXTERNAL_ENTRIES),
            new AllowedInclude(new UserSchema(), ExternalProfile::RELATION_USER),
        ];
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
                new IdField($this, ExternalProfile::ATTRIBUTE_ID),
                new ExternalProfileUsernameField($this),
                new ExternalProfileSiteField($this),
                new ExternalProfileVisibilityField($this),
            ],
        );
    }
}