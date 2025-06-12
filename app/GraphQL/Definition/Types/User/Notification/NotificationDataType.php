<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\User\Notification;

use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\User\Notification\NotificationData\NotificationDataBodyField;
use App\GraphQL\Definition\Fields\User\Notification\NotificationData\NotificationDataImageField;
use App\GraphQL\Definition\Fields\User\Notification\NotificationData\NotificationDataTitleField;
use App\GraphQL\Definition\Types\BaseType;

/**
 * Class NotificationDataType.
 */
class NotificationDataType extends BaseType implements HasFields
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the JSON data of the notification";
    }

    /**
     * The fields of the type.
     *
     * @return array<int, Field>
     */
    public function fields(): array
    {
        return [
            new NotificationDataTitleField(),
            new NotificationDataBodyField(),
            new NotificationDataImageField(),
        ];
    }
}
