<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Studio;

/**
 * @extends WikiUpdatedEvent<Studio>
 */
class StudioUpdated extends WikiUpdatedEvent
{
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
        $this->initializeEmbedFields($studio);
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
        return "Studio '**{$this->getModel()->getName()}**' has been updated.";
    }
}
