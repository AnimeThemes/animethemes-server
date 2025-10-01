<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\List\ExternalProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Notifications\Notification;

class ExternalProfileSyncedNotification extends Notification implements Arrayable, ShouldQueue
{
    use Queueable;

    public function __construct(
        public ExternalProfile $profile,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'profileId' => $this->profile->getKey(),
            'profileName' => $this->profile->getName(),
        ];
    }
}
