<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\User\Notification\NotificationData;

use App\GraphQL\Definition\Fields\StringField;

class NotificationDataImageField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct('image');
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The image URL to display with the notification';
    }
}
