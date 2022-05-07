<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Studio;

/**
 * Class StudioRestored.
 *
 * @extends WikiRestoredEvent<Studio>
 */
class StudioRestored extends WikiRestoredEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Studio  $studio
     */
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Studio
     */
    public function getModel(): Studio
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
        return "Studio '**{$this->getModel()->getName()}**' has been restored.";
    }
}
