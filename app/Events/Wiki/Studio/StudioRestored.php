<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Studio;

/**
 * @extends WikiRestoredEvent<Studio>
 */
class StudioRestored extends WikiRestoredEvent
{
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Studio
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Studio '**{$this->getModel()->getName()}**' has been restored.";
    }
}
