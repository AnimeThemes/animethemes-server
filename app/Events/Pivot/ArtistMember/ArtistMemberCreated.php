<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistMember;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ArtistMemberCreated.
 */
class ArtistMemberCreated extends ArtistMemberEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $artist = $this->getArtist();
        $member = $this->getMember();

        return DiscordMessage::create('', [
            'description' => "Member '**{$member->getName()}**' has been attached to Artist '**{$artist->getName()}**'.",
            'color' => EmbedColor::GREEN,
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get('services.discord.db_updates_discord_channel');
    }
}
