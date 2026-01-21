<?php

declare(strict_types=1);

namespace App\Events\Auth\Sanction;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\NotifiesUsersEvent;
use App\Enums\Discord\EmbedColor;
use App\Models\Auth\Sanction;
use App\Models\Auth\User;
use App\Notifications\Auth\ProhibitionOrSanctionNotification;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Kyrch\Prohibition\Events\ModelSanctionTriggered as BaseModelSanctionTriggered;
use NotificationChannels\Discord\DiscordMessage;

class ModelSanctionTriggered extends BaseModelSanctionTriggered implements DiscordMessageEvent, NotifiesUsersEvent
{
    /**
     * @param  User  $model
     * @param  Sanction  $sanction
     * @param  User  $moderator
     */
    public function __construct(
        public Model $model,
        public mixed $sanction,
        public ?DateTimeInterface $expiresAt = null,
        public ?string $reason = null,
        public ?Model $moderator = null,
    ) {
        parent::__construct($model, $sanction, $expiresAt, $reason, $moderator);
    }

    public function getDiscordMessage(): DiscordMessage
    {
        return DiscordMessage::create('', [
            'description' => "Sanction '**{$this->sanction->name}**' triggered for user '**{$this->model->getName()}**'. Reason: {$this->reason}",
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
        $this->model->notify(new ProhibitionOrSanctionNotification($this->sanction, $this->reason, $this->expiresAt));
    }
}
