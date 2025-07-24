<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class VideoThrottled implements DiscordMessageEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(protected Video $video, protected string $user) {}

    /**
     * Get Discord message payload.
     */
    public function getDiscordMessage(): DiscordMessage
    {
        return DiscordMessage::create('', [
            'description' => "Video '**{$this->video->getName()}**' throttled for user '**$this->user**'",
            'color' => EmbedColor::YELLOW->value,
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Determine if the message should be sent.
     */
    public function shouldSendDiscordMessage(): bool
    {
        return true;
    }
}
