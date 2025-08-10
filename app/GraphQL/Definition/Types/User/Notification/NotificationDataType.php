<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\User\Notification;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\User\Notification\NotificationData\NotificationDataBodyField;
use App\GraphQL\Definition\Fields\User\Notification\NotificationData\NotificationDataImageField;
use App\GraphQL\Definition\Fields\User\Notification\NotificationData\NotificationDataTitleField;
use App\GraphQL\Definition\Types\BaseType;

class NotificationDataType extends BaseType
{
    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Represents the JSON data of the notification';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new NotificationDataTitleField(),
            new NotificationDataBodyField(),
            new NotificationDataImageField(),
        ];
    }
}
