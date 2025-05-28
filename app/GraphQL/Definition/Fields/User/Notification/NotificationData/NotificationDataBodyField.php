<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\User\Notification\NotificationData;

use App\GraphQL\Definition\Fields\StringField;

/**
 * Class NotificationDataBodyField.
 */
class NotificationDataBodyField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct('body', nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The content of the notification';
    }
}
