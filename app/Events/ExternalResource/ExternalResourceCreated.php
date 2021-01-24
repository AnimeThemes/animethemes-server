<?php

namespace App\Events\ExternalResource;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class ExternalResourceCreated extends ExternalResourceEvent implements DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $resource = $this->getResource();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Resource Created', [
            'description' => "Resource '{$resource->link}' has been created.",
        ]);
    }
}
