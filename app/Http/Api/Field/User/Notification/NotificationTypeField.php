<?php

declare(strict_types=1);

namespace App\Http\Api\Field\User\Notification;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\User\Notification;

class NotificationTypeField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Notification::ATTRIBUTE_TYPE);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match user notifications on query.
        return true;
    }
}
