<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\User\Notification;

use App\GraphQL\Definition\Fields\JsonField;
use App\GraphQL\Definition\Types\User\Notification\NotificationDataType;
use App\Models\User\Notification;
use GraphQL\Type\Definition\Type;

/**
 * Class NotificationDataField.
 */
class NotificationDataField extends JsonField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Notification::ATTRIBUTE_DATA, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The JSON data of the notification';
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        return new NotificationDataType();
    }
}
