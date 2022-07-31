<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Audio;

/**
 * Class AudioUpdated.
 *
 * @extends WikiUpdatedEvent<Audio>
 */
class AudioUpdated extends WikiUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Audio  $audio
     */
    public function __construct(Audio $audio)
    {
        parent::__construct($audio);
        $this->initializeEmbedFields($audio);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Audio
     */
    public function getModel(): Audio
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Audio '**{$this->getModel()->getName()}**' has been updated.";
    }
}
