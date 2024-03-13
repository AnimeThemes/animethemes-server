<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\List\External;

use App\Http\Api\Field\Field;
use App\Http\Api\Field\List\ExternalProfile\ExternalEntry\ExternalEntryWatchStatusField;
use App\Http\Api\Field\List\ExternalProfile\ExternalEntry\ExternalEntryIdField;
use App\Http\Api\Field\List\ExternalProfile\ExternalEntry\ExternalEntryIsFavouriteField;
use App\Http\Api\Field\List\ExternalProfile\ExternalEntry\ExternalEntryScoreField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Auth\UserSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\ExternalProfileSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\List\External\Resource\ExternalEntryResource;
use App\Models\List\External\ExternalEntry;

/**
 * Class ExternalEntrySchema.
 */
class ExternalEntrySchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ExternalEntryResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), ExternalEntry::RELATION_ANIME),
            new AllowedInclude(new ExternalProfileSchema(), ExternalEntry::RELATION_EXTERNAL_PROFILE),
            new AllowedInclude(new UserSchema(), ExternalEntry::RELATION_USER),
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
                new ExternalEntryIdField($this),
                new ExternalEntryScoreField($this),
                new ExternalEntryIsFavouriteField($this),
                new ExternalEntryWatchStatusField($this),
            ],
        );
    }
}
