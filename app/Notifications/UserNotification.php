<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\Models\User\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification implements Arrayable, ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public NotificationType $type,
        public ?string $image = null,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array
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
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type->value,
            'image' => $this->image,
        ];
    }
}
