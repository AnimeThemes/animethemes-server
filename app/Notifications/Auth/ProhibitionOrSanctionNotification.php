<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use App\Models\Auth\Prohibition;
use App\Models\Auth\Sanction;
use DateTimeInterface;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

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

        $typeCapitalized = Str::ucfirst($type);

        return new MailMessage()
            ->subject("Account {$typeCapitalized} Issued")
            ->greeting("Dear {$notifiable->getName()},")
            ->line("A {$type} ({$this->issued->name}) has been applied to your account.")
            ->line($this->getExpiresAtLine($type))
            ->line("Reason: {$this->reason}");
    }

    protected function getExpiresAtLine(string $type): string
    {
        if (! $this->expiresAt instanceof DateTimeInterface) {
            return "This {$type} is permanent.";
        }

        $human = Date::instance($this->expiresAt)->diffForHumans(syntax: Carbon::DIFF_ABSOLUTE, parts: 4);

        return "This {$type} will expire in {$human}.";
    }
}
