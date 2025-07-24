<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\User\Notification\NotificationData;

use App\GraphQL\Definition\Fields\StringField;

class NotificationDataBodyField extends StringField
{
    public function __construct()
    {
        parent::__construct('body', nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The content of the notification';
    }
}
