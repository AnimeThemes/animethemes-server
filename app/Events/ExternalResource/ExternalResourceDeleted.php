<?php

namespace App\Events\ExternalResource;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class ExternalResourceDeleted extends ExternalResourceEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $resource = $this->getResource();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Resource Deleted', [
            'description' => "Resource '{$resource->getName()}' has been deleted.",
        ]);
    }
}
