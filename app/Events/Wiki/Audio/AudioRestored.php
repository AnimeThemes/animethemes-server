<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Audio;

/**
 * Class AudioRestored.
 *
 * @extends WikiRestoredEvent<Audio>
 */
class AudioRestored extends WikiRestoredEvent
{
    public function __construct(Audio $audio)
    {
        parent::__construct($audio);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Audio
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Audio '**{$this->getModel()->getName()}**' has been restored.";
    }
}
