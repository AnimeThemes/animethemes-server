<?php

namespace App\Events\Pivot\ArtistMember;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class ArtistMemberCreated extends ArtistMemberEvent implements DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

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
        return DiscordMessage::create('Member Attached', [
            'description' => "Member '{$member->name}' has been attached to Artist '{$artist->name}'.",
        ]);
    }
}
