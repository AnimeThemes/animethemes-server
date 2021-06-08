<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistMember;

use App\Concerns\Services\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Pivots\ArtistMember;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ArtistMemberUpdated.
 */
class ArtistMemberUpdated extends ArtistMemberEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param ArtistMember $artistMember
     * @return void
     */
    public function __construct(ArtistMember $artistMember)
    {
        parent::__construct($artistMember);
        $this->initializeEmbedFields($artistMember);
    }

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
            'description' => "Member '**{$member->getName()}**' for Artist '**{$artist->getName()}**' has been updated.",
            'fields' => $this->getEmbedFields(),
            'color' => EmbedColor::YELLOW,
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
