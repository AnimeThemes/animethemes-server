<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\User;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\User\Notification\NotificationDataField;
use App\GraphQL\Definition\Fields\User\Notification\NotificationReadAtField;
use App\GraphQL\Definition\Relations\MorphToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\User\Notification;

class NotificationType extends EloquentType implements HasFields, HasRelations
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Represents a notification that is sent to the user.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new MorphToRelation(new UserType(), Notification::RELATION_NOTIFIABLE, 'user'),
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
            new NotificationReadAtField(),
            new NotificationDataField(),
        ];
    }
}
