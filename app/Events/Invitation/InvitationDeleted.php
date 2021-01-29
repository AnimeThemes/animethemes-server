<?php

namespace App\Events\Invitation;

use App\Contracts\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class InvitationDeleted extends InvitationEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $invitation = $this->getInvitation();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Invitation Deleted', [
            'description' => "Invitation '{$invitation->getName()}' has been deleted.",
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel()
    {
        return Config::get('services.discord.admin_discord_channel');
    }
}
