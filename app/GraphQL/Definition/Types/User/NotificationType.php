<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\User;

use App\GraphQL\Definition\Fields\User\Notification\NotificationDataField;
use App\GraphQL\Definition\Fields\User\Notification\NotificationReadAtField;
use App\GraphQL\Definition\Relations\MorphToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\BaseType;
use App\Models\User\Notification;

/**
 * Class NotificationType.
 */
class NotificationType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a notification that is sent to the user.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
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
     * @return array
     */
    public function fields(): array
    {
        return [
            new NotificationReadAtField(),
            new NotificationDataField(),
        ];
    }
}
