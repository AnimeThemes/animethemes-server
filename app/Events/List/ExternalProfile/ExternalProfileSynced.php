<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile;

use App\Contracts\Events\NotifiesUsersEvent;
use App\Enums\Models\User\NotificationType;
use App\Models\List\ExternalProfile;
use App\Notifications\UserNotification;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class ExternalProfileSynced.
 */
class ExternalProfileSynced implements NotifiesUsersEvent
{
    use Dispatchable;

    /**
     * Create new event instance.
     *
     * @param  ExternalProfile  $profile
     */
    public function __construct(protected ExternalProfile $profile)
    {
    }

    /**
     * Notify the users.
     *
     * @return void
     */
    public function notify(): void
    {
        $profile = $this->profile;

        $notification = new UserNotification(
            'External Profile Synced',
            "Your external profile [{$profile->getName()}]({$profile->getClientUrl()}) has been synced.",
            NotificationType::SYNCED_PROFILE,
        );

        $profile->user?->notifyNow($notification);
    }
}
