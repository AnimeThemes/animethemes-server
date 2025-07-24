<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\User\Notification\NotificationData;

use App\GraphQL\Definition\Fields\StringField;

class NotificationDataTitleField extends StringField
{
    public function __construct()
    {
        parent::__construct('title', nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The title of the notification';
    }
}
