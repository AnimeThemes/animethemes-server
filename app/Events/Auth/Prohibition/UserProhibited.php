<?php

declare(strict_types=1);

namespace App\Events\Auth\Prohibition;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\NotifiesUsersEvent;
use App\Enums\Discord\EmbedColor;
use App\Events\BaseEvent;
use App\Models\Auth\Prohibition;
use App\Models\Auth\User;
use App\Notifications\Auth\ProhibitionOrSanctionNotification;
use DateTimeInterface;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class UserProhibited extends BaseEvent implements DiscordMessageEvent, NotifiesUsersEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public Prohibition $prohibition,
        public ?DateTimeInterface $expiresAt = null,
        public ?string $reason = null,
        public ?User $moderator = null,
    ) {
        parent::__construct($user);
    }

    public function getDiscordMessage(): DiscordMessage
    {
        return DiscordMessage::create('', [
            'description' => "Prohibition '**{$this->prohibition->name}**' triggered for user '**{$this->user->getName()}**'. Reason: {$this->reason}",
            'color' => EmbedColor::GREEN->value,
        ]);
    }

    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
    }

    public function shouldSendDiscordMessage(): bool
    {
        return true;
    }

    public function notify(): void
    {
        $this->user->notify(new ProhibitionOrSanctionNotification($this->prohibition, $this->reason, $this->expiresAt));
    }
}
