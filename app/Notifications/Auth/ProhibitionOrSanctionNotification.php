<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use App\Models\Auth\Prohibition;
use App\Models\Auth\Sanction;
use DateTimeInterface;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProhibitionOrSanctionNotification extends Notification
{
    public function __construct(
        public Prohibition|Sanction $issued,
        public string $reason,
        public ?DateTimeInterface $expiresAt = null,
    ) {}

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $type = $this->issued instanceof Prohibition ? 'prohibition' : 'sanction';

        return new MailMessage()
            ->subject('Account Prohibition or Sanction Issued')
            ->greeting("Dear {$notifiable->getName()},")
            ->line("Your account has been issued a {$type} ({$this->issued->name}).")
            ->line($this->getExpiresAtLine($type))
            ->line("Reason: {$this->reason}");
    }

    protected function getExpiresAtLine(string $type): string
    {
        if (! $this->expiresAt instanceof DateTimeInterface) {
            return "This {$type} is permanent.";
        }

        $expiresFormat = $this->expiresAt->format('Y-m-d H:i:s');

        return "This {$type} will expire on {$expiresFormat}.";
    }
}
