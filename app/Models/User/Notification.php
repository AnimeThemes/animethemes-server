<?php

declare(strict_types=1);

namespace App\Models\User;

use Database\Factories\User\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Class Notification.
 *
 * @method static NotificationFactory factory(...$parameters)
 */
class Notification extends DatabaseNotification
{
    use HasFactory;

    final public const TABLE = 'notifications';

    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_TYPE = 'type';
    final public const ATTRIBUTE_NOTIFIABLE_TYPE = 'notifiable_type';
    final public const ATTRIBUTE_NOTIFIABLE_ID = 'notifiable_id';
    final public const ATTRIBUTE_DATA = 'data';
    final public const ATTRIBUTE_READ_AT = 'read_at';

    final public const RELATION_NOTIFIABLE = 'notifiable';
}
