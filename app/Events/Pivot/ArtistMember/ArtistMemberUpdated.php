<?php

namespace App\Events\Pivot\ArtistMember;

use App\Contracts\Events\DiscordMessageEvent;
use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Pivots\ArtistMember;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class ArtistMemberUpdated extends ArtistMemberEvent implements DiscordMessageEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Pivots\ArtistMember $artistMember
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
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $artist = $this->getArtist();
        $member = $this->getMember();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Member Updated', [
            'description' => "Member '{$member->getName()}' for Artist '{$artist->getName()}' has been updated.",
            'fields' => $this->getEmbedFields(),
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel()
    {
        return Config::get('services.discord.db_updates_discord_channel');
    }
}
