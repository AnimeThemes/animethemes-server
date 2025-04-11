<?php

declare(strict_types=1);

namespace App\Http\Api\Field\User\Notification;

use App\Http\Api\Field\JsonField;
use App\Http\Api\Schema\Schema;
use App\Models\User\Notification;

/**
 * Class NotificationDataField.
 */
class NotificationDataField extends JsonField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Notification::ATTRIBUTE_DATA);
    }
}
