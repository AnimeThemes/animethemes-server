<?php

declare(strict_types=1);

namespace App\Http\Api\Field\User\Notification;

use App\Http\Api\Field\JsonField;
use App\Http\Api\Schema\Schema;
use App\Models\User\Notification;

class NotificationDataField extends JsonField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Notification::ATTRIBUTE_DATA);
    }
}
