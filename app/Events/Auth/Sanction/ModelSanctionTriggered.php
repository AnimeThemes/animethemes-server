<?php

declare(strict_types=1);

namespace App\Events\Auth\Prohibition;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Config;
use Kyrch\Prohibition\Events\ModelSanctionTriggered as BaseModelSanctionTriggered;
use NotificationChannels\Discord\DiscordMessage;

class ModelSanctionTriggered extends BaseModelSanctionTriggered implements DiscordMessageEvent
{
    public function getDiscordMessage(): DiscordMessage
    {
        /** @var User $user */
        $user = $this->model;

        return DiscordMessage::create('', [
            'description' => "Sanction '**{$this->sanction->name}**' triggered for user '**{$user->getName()}**'. Reason: {$this->reason}",
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
}
