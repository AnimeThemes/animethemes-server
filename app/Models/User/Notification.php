<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Models\List\ExternalProfile;
use Database\Factories\User\NotificationFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Arr;

/**
 * @method static NotificationFactory factory(...$parameters)
 */
class Notification extends DatabaseNotification
{
    use HasFactory;

    final public const string TABLE = 'notifications';

    final public const string ATTRIBUTE_ID = 'id';
    final public const string ATTRIBUTE_TYPE = 'type';
    final public const string ATTRIBUTE_NOTIFIABLE_TYPE = 'notifiable_type';
    final public const string ATTRIBUTE_NOTIFIABLE_ID = 'notifiable_id';
    final public const string ATTRIBUTE_DATA = 'data';
    final public const string ATTRIBUTE_READ_AT = 'read_at';

    final public const string RELATION_PROFILE = 'profile';
    final public const string RELATION_NOTIFIABLE = 'notifiable';

    /**
     * Virtual attribute to use in relations.
     */
    protected function profileId(): Attribute
    {
        return Attribute::make(fn () => Arr::get($this->getAttribute(self::ATTRIBUTE_DATA), 'profileId'));
    }

    /**
     * Virtual relation to the profile.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(ExternalProfile::class, 'profile_id');
    }
}
