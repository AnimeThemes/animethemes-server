<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\User;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\User\Notification\NotificationDataField;
use App\GraphQL\Definition\Fields\User\Notification\NotificationReadAtField;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Support\Relations\MorphToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\User\Notification;

class NotificationType extends EloquentType
{
    /**
     * The description of the type.
     */
    public function description(): string
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
            new MorphToRelation(new UserType(), Notification::RELATION_NOTIFIABLE)
                ->renameTo('user'),
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
            new NotificationReadAtField(),
            new NotificationDataField(),
        ];
    }
}
