<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile;

use App\Contracts\Events\NotifiesUsersEvent;
use App\Models\List\ExternalProfile;
use App\Notifications\ExternalProfileSyncedNotification;
use Illuminate\Foundation\Events\Dispatchable;

class ExternalProfileSynced implements NotifiesUsersEvent
{
    use Dispatchable;

    public function __construct(protected ExternalProfile $profile) {}

    public function notify(): void
    {
        $profile = $this->profile;

        $profile->user?->notifyNow(
            new ExternalProfileSyncedNotification($profile)
        );
    }
}
