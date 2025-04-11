<?php

declare(strict_types=1);

namespace App\Http\Api\Field\User\Notification;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\User\Notification;

/**
 * Class NotificationNotifiableIdField.
 */
class NotificationNotifiableIdField extends Field implements SelectableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Notification::ATTRIBUTE_NOTIFIABLE_ID);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Query  $query
     * @param  Schema  $schema
     * @return bool
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to filter the user notifications on query.
        return true;
    }
}
