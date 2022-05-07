<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Studio;

/**
 * Class StudioUpdated.
 *
 * @extends WikiUpdatedEvent<Studio>
 */
class StudioUpdated extends WikiUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Studio  $studio
     */
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
        $this->initializeEmbedFields($studio);
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
        return "Studio '**{$this->getModel()->getName()}**' has been updated.";
    }
}
