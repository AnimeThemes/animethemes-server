<?php

namespace App\Events\ExternalResource;

use App\Discord\Events\DiscordMessageEvent;
use App\Discord\Traits\HasAttributeUpdateEmbedFields;
use App\Models\ExternalResource;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class ExternalResourceUpdated extends ExternalResourceEvent implements DiscordMessageEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\ExternalResource $resource
     * @return void
     */
    public function __construct(ExternalResource $resource)
    {
        parent::__construct($resource);
        $this->initializeEmbedFields($resource);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $resource = $this->getResource();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Resource Updated', [
            'description' => "Resource '{$resource->getName()}' has been updated.",
            'fields' => $this->getEmbedFields(),
        ]);
    }
}
