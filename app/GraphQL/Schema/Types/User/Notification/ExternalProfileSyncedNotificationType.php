<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\User\Notification;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\User\Notification\ExternalProfileSynced\ExternalProfileSyncedProfileIdField;
use App\GraphQL\Schema\Fields\User\Notification\ExternalProfileSynced\ExternalProfileSyncedProfileNameField;
use App\GraphQL\Schema\Fields\User\Notification\NotificationReadAtField;
use App\GraphQL\Schema\Fields\User\Notification\NotificationTypeField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\List\ExternalProfileType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\User\Notification;

class ExternalProfileSyncedNotificationType extends EloquentType
{
    public function description(): string
    {
        return 'Represents a notification that is sent to the user when a profile is synced.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new ExternalProfileType(), Notification::RELATION_PROFILE)
                ->notNullable(),
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
            new NotificationTypeField(),
            new ExternalProfileSyncedProfileIdField(),
            new ExternalProfileSyncedProfileNameField(),
            new NotificationReadAtField(),
            new CreatedAtField(),
        ];
    }
}
